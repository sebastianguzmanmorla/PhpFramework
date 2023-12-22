<?php

namespace PhpFramework\Request;

use PhpFramework\Attributes\Hashid;
use PhpFramework\Attributes\Parameter;
use PhpFramework\Router;
use ReflectionClass;

class JsonRequest implements IRequest
{
    public function __construct($Json)
    {
        $Json = json_decode($Json, true);

        $reflection = new ReflectionClass($this::class);

        foreach ($reflection->getProperties() as $property) {
            $Value = null;

            $Attributes = $property->getAttributes();

            if (!empty($Attributes)) {
                $Attribute = $Attributes[0]->newInstance();

                if ($Attribute instanceof Parameter || $Attribute instanceof Hashid) {
                    $Value = $Attribute->ParameterValue($property->getName());
                }
            } else {
                $Value = isset($Json[$property->getName()]) ? $Json[$property->getName()] : null;
            }

            if ($Value !== null) {
                switch ($property->getType()->getName()) {
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
                        $Value = Router::XssClean($Value);

                        break;
                    case 'string':
                        $Value = Router::XssClean((string) $Value);

                        break;
                    default:
                        break;
                }
                $property->setValue($this, $Value);
            }
        }

        return $this;
    }
}
