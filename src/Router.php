<?php

namespace PhpFramework;

use DateTime;
use PhpFramework\Attributes\Hashid;
use PhpFramework\Attributes\Parameter;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Request\IRequestFilter;
use PhpFramework\Response\ErrorJsonResponse;
use PhpFramework\Response\ExceptionResponse;
use PhpFramework\Response\IResponse;
use PhpFramework\Response\JsonResponse;
use PhpFramework\Response\StatusCode;
use ReflectionClass;
use Throwable;

class Router
{
    public const Route = 'route';
    public const Controller = '0';
    public const Method = '1';

    public static array $Routes = [];

    public static function XssClean($Input)
    {
        if (is_array($Input)) {
            foreach ($Input as $Key => $Value) {
                $Input[$Key] = static::XssClean($Value);
            }
        } else {
            $Input = htmlspecialchars($Input);
        }

        return $Input;
    }

    public static function Process(): IResponse
    {
        $Route = null;

        try {
            if (!isset($_GET[self::Route]) || $_GET[self::Route] == null) {
                $_GET[self::Route] = Route::$DefaultRoute;
            }
            if (!isset($_SERVER['REQUEST_METHOD'])) {
                $_SERVER['REQUEST_METHOD'] = Route::$DefaultMethod;
            }

            if (isset(static::$Routes[$_GET[self::Route]][$_SERVER['REQUEST_METHOD']])) {
                $Route = static::$Routes[$_GET[self::Route]][$_SERVER['REQUEST_METHOD']];

                foreach ($Route[self::Method]->getAttributes() as $Attribute) {
                    $Class = new ReflectionClass($Attribute->getName());
                    if ($Class->isSubclassOf(IRequestFilter::class)) {
                        $IRequestFilter = $Attribute->newInstance();
                        $IRequestFilterClass = new ReflectionClass($IRequestFilter::class);
                        foreach ($IRequestFilterClass->getProperties() as $IRequestFilterProperty) {
                            $Singleton = $IRequestFilterProperty->getAttributes(Singleton::class);
                            if (!empty($Singleton)) {
                                $ControllerPropertyType = $IRequestFilterProperty->getType();
                                $ControllerPropertyValue = Singleton::Get($ControllerPropertyType->getName());
                                $IRequestFilterProperty->setValue($IRequestFilter, $ControllerPropertyValue);
                            }
                        }
                        $Filter = $IRequestFilter->Filter();
                        if ($Filter !== null) {
                            return $Filter;
                        }
                    }
                }

                $Controller = $Route[self::Controller]->newInstance();

                foreach ($Route[self::Controller]->getProperties() as $ControllerProperty) {
                    $Singleton = $ControllerProperty->getAttributes(Singleton::class);
                    if (!empty($Singleton)) {
                        $ControllerPropertyType = $ControllerProperty->getType();
                        $ControllerPropertyValue = Singleton::Get($ControllerPropertyType->getName());
                        $ControllerProperty->setValue($Controller, $ControllerPropertyValue);
                    }
                }

                $RouteParameters = [];

                $Hashids = isset($_GET[Hashids::IdParameter]) ? Hashids::Decode($_GET[Hashids::IdParameter]) : [];

                foreach ($Route[self::Method]->GetParameters() as $RouteParameter) {
                    $RouteParameterType = $RouteParameter->getType();

                    if (!$RouteParameterType->isBuiltin() && $RouteParameterType->getName() != 'DateTime') {
                        $Value = isset($_GET[$RouteParameter->getName()]) ? $_GET[$RouteParameter->getName()] : file_get_contents('php://input');

                        $RouteParameterClass = new ReflectionClass($RouteParameterType->getName());
                        $RouteParameters[$RouteParameter->getName()] = $RouteParameterClass->newInstance($Value);
                    } else {
                        $RouteMethod = $_SERVER['REQUEST_METHOD'];

                        $RouteParameterAttributes = $RouteParameter->getAttributes(Parameter::class);

                        if (isset($RouteParameterAttributes[0])) {
                            $RouteMethod = $RouteParameterAttributes[0]->newInstance()->Method->value;
                        }

                        $HashidAttributes = $RouteParameter->getAttributes(Hashid::class);

                        if (isset($HashidAttributes[0])) {
                            $RouteMethod = $HashidAttributes[0]->newInstance()->Method->value;
                        }

                        $Value = null;

                        switch ($RouteMethod) {
                            case 'GET':
                                $Value = isset($_GET[$RouteParameter->getName()]) ? $_GET[$RouteParameter->getName()] : null;

                                break;
                            case 'POST':
                                $Value = isset($_POST[$RouteParameter->getName()]) ? $_POST[$RouteParameter->getName()] : null;

                                break;
                            default:
                                break;
                        }

                        $RouteParameters[$RouteParameter->getName()] = null;

                        if (!empty($HashidAttributes) && $RouteMethod == 'GET' && $Value === null) {
                            $RouteParameters[$RouteParameter->getName()] = array_shift($Hashids) ?? null;

                            continue;
                        }

                        if (!empty($HashidAttributes) && $Value !== null) {
                            $RouteParameters[$RouteParameter->getName()] = Hashids::Decode($Value)[0];

                            continue;
                        }

                        if ($Value !== null && $Value !== '') {
                            switch ($RouteParameterType->getName()) {
                                case 'int':
                                    $RouteParameters[$RouteParameter->getName()] = (int) $Value;

                                    break;
                                case 'float':
                                    $RouteParameters[$RouteParameter->getName()] = (float) $Value;

                                    break;
                                case 'bool':
                                    $RouteParameters[$RouteParameter->getName()] = (bool) $Value;

                                    break;
                                case 'array':
                                    $RouteParameters[$RouteParameter->getName()] = static::XssClean($Value);

                                    break;
                                case 'string':
                                    $RouteParameters[$RouteParameter->getName()] = static::XssClean((string) $Value);

                                    break;
                                case 'DateTime':
                                    $RouteParameters[$RouteParameter->getName()] = new DateTime($Value);

                                    break;
                                default:
                                    $RouteParameters[$RouteParameter->getName()] = $Value;

                                    break;
                            }
                        }
                    }
                }

                return $Route[self::Method]->invoke($Controller, ...$RouteParameters);
            }

            throw new Exception('Route not found: ' . static::XssClean((string) ($_GET[self::Route])), StatusCode::NotFound);
        } catch (Throwable $Exception) {
            $StatusCode = $Exception instanceof Exception ? $Exception->StatusCode : StatusCode::InternalServerError;

            if ($Route !== null) {
                $RouteName = $Route[self::Method]->getreturnType()->getName();
                $RouteParameterClass = new ReflectionClass($RouteName);
                if ($RouteParameterClass->isSubclassOf(JsonResponse::class) || $RouteName == JsonResponse::class) {
                    return new ErrorJsonResponse($StatusCode, $Exception->getMessage());
                }
            }

            return new ExceptionResponse($StatusCode, $Exception);
        }
    }
}
