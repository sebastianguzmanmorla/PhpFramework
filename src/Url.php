<?php

namespace PhpFramework;

use ArrayObject;
use Closure;
use Exception;
use PhpFramework\Database\Helpers\SourceReader;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class Url
{
    public string $Url;

    public function __construct(Closure|string $Url)
    {
        if ($Url instanceof Closure) {
            $Reflection = new ReflectionFunction($Url);

            $ParameterClass = null;

            $Parameter = $Reflection->getParameters();

            if (count($Parameter) != 1) {
                throw new Exception('El closure debe tener un parametro.', false);
            }

            $Parameter = $Parameter[0];

            $ParameterType = $Parameter->getType();
            if ($ParameterType !== null) {
                $ParameterClass = new ReflectionClass($ParameterType->getName());
                if (!$ParameterClass->isSubclassOf(Controller::class)) {
                    throw new Exception('El parametro no es de la clase requerida.', false);
                }
            } else {
                throw new Exception('El parametro no es de la clase requerida.', false);
            }

            $ParameterMethod = null;

            $UsedVariables = [];

            foreach ($Reflection->getClosureUsedVariables() as $Variable => $Value) {
                $UsedVariables['$' . $Variable] = $Value;
            }

            $Tokens = SourceReader::readClosure($Reflection, 0);
            $Tokens = new ArrayObject($Tokens);
            $Tokens = $Tokens->getIterator();

            $Token = fn () => $Tokens->current();

            while ($Tokens->valid()) {
                if ($Token()->id == T_STRING) {
                    $ParameterMethod = $Token()->text;
                    $Tokens->next();

                    break;
                }
                $Tokens->next();
            }

            if ($ParameterMethod == null) {
                throw new Exception('No se pudo obtener el metodo del parametro.', false);
            }

            $ParameterMethod = new ReflectionMethod($ParameterClass->getName(), $ParameterMethod);

            $RouteAttribute = $ParameterMethod->getAttributes(Route::class);

            if (empty($RouteAttribute)) {
                throw new Exception('El metodo debe tener una accion definida.', false);
            }

            $RouteAttribute = $RouteAttribute[0]->newInstance();

            if ($Token()->text != '(') {
                throw new Exception('No se pudo obtener el metodo del parametro.', false);
            }

            $Tokens->next();

            $Parameters = [
                Router::Route => $RouteAttribute->Route,
            ];

            while ($Tokens->valid()) {
                if ($Token()->id == T_STRING) {
                    $Parameter = $Token()->text;
                    $Tokens->next();

                    if ($Token()->text != ':') {
                        throw new Exception('No se pudo obtener el atributo del metodo.', false);
                    }

                    $Tokens->next();

                    while ($Tokens->valid() && $Token()->id == T_WHITESPACE) {
                        $Tokens->next();
                    }

                    if (in_array($Token()->id, [T_CONSTANT_ENCAPSED_STRING, T_LNUMBER, T_DNUMBER])) {
                        $Parameters[$Parameter] = trim($Token()->text, '"');
                    }

                    if ($Token()->id == T_VARIABLE) {
                        $Variable = $Token()->text;

                        $Expression = [];

                        $Tokens->next();

                        while (in_array($Token()->id, [T_OBJECT_OPERATOR, 91])) {
                            $Tokens->next();
                            $Expression[] = $Token()->text;
                            $Tokens->next();
                        }

                        if ($Variable == '$this') {
                            $Value = $Reflection->getClosureThis();
                            foreach ($Expression as $Expression) {
                                $Value = $Value->{$Expression};
                            }
                        } elseif (isset($UsedVariables[$Variable])) {
                            $Value = $UsedVariables[$Variable];
                            foreach ($Expression as $Expression) {
                                $Value = $Value->{$Expression};
                            }
                        } elseif (in_array($Variable, ['$_SESSION', '$_GET', '$_POST', '$_COOKIE', '$_SERVER', '$_ENV'])) {
                            switch ($Variable) {
                                case '$_SESSION':
                                    $Value = $_SESSION;

                                    break;
                                case '$_GET':
                                    $Value = $_GET;

                                    break;
                                case '$_POST':
                                    $Value = $_POST;

                                    break;
                                case '$_COOKIE':
                                    $Value = $_COOKIE;

                                    break;
                                case '$_SERVER':
                                    $Value = $_SERVER;

                                    break;
                                case '$_ENV':
                                    $Value = $_ENV;

                                    break;
                            }
                            foreach ($Expression as $Expression) {
                                $Expression = trim($Expression, "\"'");
                                $Value = $Value[$Expression] ?? null;
                            }
                        }

                        $Parameters[$Parameter] = $Value;
                    }
                }

                if ($Token()->text == ')') {
                    break;
                }

                $Tokens->next();
            }

            $Hashids = [];

            foreach ($ParameterMethod->getParameters() as $Parameter) {
                $Hashid = $Parameter->getAttributes(Hashid::class);

                if (!empty($Hashid)) {
                    $Hashids[] = isset($Parameters[$Parameter->getName()]) ? $Parameters[$Parameter->getName()] : 0;

                    unset($Parameters[$Parameter->getName()]);

                    continue;
                }
            }

            if (!empty($Hashids)) {
                $Parameters['id'] = Hashids::encode(...$Hashids);
            }

            $this->Url = !empty($Parameters) ? '?' . http_build_query($Parameters) : '';
        } else {
            $this->Url = $Url;
        }
    }

    public function __toString(): string
    {
        return $this->Url;
    }
}
