<?php

namespace PhpFramework;

use DateTime;
use PhpFramework\Attributes\Hashid;
use PhpFramework\Attributes\Parameter;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Request\IRequestFilter;
use PhpFramework\Request\Trait\Request as TraitRequest;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\ExceptionResponse;
use PhpFramework\Response\Interface\IResponse;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Throwable;

final class Router
{
    use TraitRequest;

    public const Route = 'route';
    public const Controller = '0';
    public const Method = '1';

    public static array $Routes = [];

    public static function BaseUrl()
    {
        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
        } else {
            $protocol = 'http';
        }

        return $protocol . '://' . $_SERVER['HTTP_HOST'] . '/';
    }

    public static function XssClean($Input)
    {
        if (is_array($Input)) {
            foreach ($Input as $Key => $Value) {
                $Input[$Key] = self::XssClean($Value);
            }
        } else {
            $Input = htmlspecialchars($Input);
        }

        return $Input;
    }

    public static function RequestProcess(
        Parameter $Parameter,
        mixed &$Context,
        ?ReflectionNamedType $Type = null
    ): void {
        if ($Type !== null) {
            $Value = $Parameter->Value();

            if ($Value !== null) {
                switch ($Type->getName()) {
                    case 'int':
                        $Value = (int) $Value;

                        break;
                    case 'float':
                        $Value = (float) $Value;

                        break;
                    case 'bool':
                        $Value = (bool) $Value;

                        break;
                    case 'array':
                        $Value = self::XssClean($Value);

                        break;
                    case 'string':
                        $Value = self::XssClean((string) $Value);

                        break;
                    case 'DateTime':
                        $Value = new DateTime($Value);

                        break;
                    default:
                        break;
                }
            }

            $Context = $Value;
        } else {
            $ReflectionClass = new ReflectionClass($Context::class);

            foreach ($ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
                $PropertyType = $Property->getType();

                if ($PropertyType instanceof ReflectionUnionType) {
                    foreach ($PropertyType->getTypes() as $Type) {
                        if ($Type->isBuiltin()) {
                            $PropertyType = $Type;

                            break;
                        }
                    }
                }

                $PropertyParameter = $Property->getAttributes(Parameter::class, ReflectionAttribute::IS_INSTANCEOF);

                $PropertyParameter = !empty($PropertyParameter) ? $PropertyParameter[0]->newInstance() : new Parameter(
                    Name: $Property->getName()
                );

                $Value = $PropertyParameter->Value();

                if ($Value !== null) {
                    switch ($PropertyType->getName()) {
                        case 'int':
                            $Value = (int) $Value;

                            break;
                        case 'float':
                            $Value = (float) $Value;

                            break;
                        case 'bool':
                            $Value = (bool) $Value;

                            break;
                        case 'array':
                            $Value = self::XssClean($Value);

                            break;
                        case 'string':
                            $Value = self::XssClean((string) $Value);

                            break;
                        case 'DateTime':
                            $Value = new DateTime($Value);

                            break;
                        default:
                            break;
                    }
                }

                $Property->setValue($Context, $Value);
            }
        }
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

            if (isset(self::$Routes[$_GET[self::Route]][$_SERVER['REQUEST_METHOD']])) {
                $Route = self::$Routes[$_GET[self::Route]][$_SERVER['REQUEST_METHOD']];

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
                    $Parameter = $RouteParameter->getAttributes(Parameter::class, ReflectionAttribute::IS_INSTANCEOF);

                    $Parameter = !empty($Parameter) ? $Parameter[0]->newInstance() : new Parameter(
                        Name: $RouteParameter->getName()
                    );

                    $RouteParameterType = $RouteParameter->getType();

                    $RouteParameterValue = null;

                    if (!$RouteParameterType->isBuiltin() && $RouteParameterType->getName() != 'DateTime') {
                        $RouteParameterClass = new ReflectionClass($RouteParameterType->getName());
                        $RouteParameterValue = $RouteParameterClass->newInstance();

                        self::RequestProcess(
                            $Parameter,
                            $RouteParameterValue,
                        );

                        $RouteParameters[$RouteParameter->getName()] = $RouteParameterValue;
                    } else {
                        if ($Parameter instanceof Hashid && $Parameter->Name === null) {
                            $RouteParameterValue = array_shift($Hashids);
                        } else {
                            self::RequestProcess(
                                $Parameter,
                                $RouteParameterValue,
                                $RouteParameterType
                            );
                        }
                    }

                    $RouteParameters[$RouteParameter->getName()] = $RouteParameterValue;
                }

                return $Route[self::Method]->invoke($Controller, ...$RouteParameters);
            }

            throw new Exception('Route not found: ' . self::XssClean((string) ($_GET[self::Route])), StatusCode::NotFound);
        } catch (Throwable $Exception) {
            $StatusCode = $Exception instanceof Exception ? $Exception->StatusCode : StatusCode::InternalServerError;

            return new ExceptionResponse($StatusCode, $Exception);
        }
    }
}
