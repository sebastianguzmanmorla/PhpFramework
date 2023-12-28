<?php

namespace PhpFramework\Database;

use ArrayAccess;
use Exception;
use JsonSerializable;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Interface\IResponse;
use ReflectionClass;
use ReflectionObject;
use stdClass;

class DbItem extends stdClass implements ArrayAccess, IResponse, JsonSerializable
{
    public function __isset(string $name): bool
    {
        $Reflection = new ReflectionObject($this);

        return $Reflection->hasProperty($name);
    }

    public function __get(string $name): mixed
    {
        $Reflection = new ReflectionObject($this);

        if ($Reflection->hasProperty($name)) {
            return $Reflection->getProperty($name)->getValue($this);
        }
        $Properties = $Reflection->getProperties();

        foreach ($Properties as $Property) {
            $Table = $Property->getValue($this);

            if ($Table instanceof DbTable) {
                $TableReflection = new ReflectionClass($Table);

                if ($TableReflection->hasProperty($name)) {
                    return $TableReflection->getProperty($name)->getValue($Table);
                }
            }
        }

        throw new Exception("Property {$name} not found");
    }

    public function __set(string $name, mixed $Value): void
    {
        $Reflection = new ReflectionObject($this);

        if ($Value instanceof DbTable || $Reflection->name == self::class) {
            $this->{$name} = $Value;
        } else {
            if ($Reflection->hasProperty($name)) {
                $Reflection->getProperty($name)->setValue($this, $Value);
            } else {
                $Properties = $Reflection->getProperties();

                foreach ($Properties as $Property) {
                    $Table = $Property->getValue($this);

                    if ($Table instanceof DbTable) {
                        $TableReflection = new ReflectionClass($Table);

                        if ($TableReflection->hasProperty($name)) {
                            $TableReflection->getProperty($name)->setValue($Table, $Value);
                        }
                    }
                }
            }
        }
    }

    public function __unset($name): void
    {
        $Reflection = new ReflectionObject($this);

        if ($Reflection->hasProperty($name)) {
            $Reflection->getProperty($name)->setValue($this, null);
        } else {
            $Properties = $Reflection->getProperties();

            foreach ($Properties as $Property) {
                $Table = $Property->getValue($this);

                if ($Table instanceof DbTable) {
                    $TableReflection = new ReflectionClass($Table);

                    if ($TableReflection->hasProperty($name)) {
                        $TableReflection->getProperty($name)->setValue($Table, null);
                    }
                }
            }
        }
    }

    public function Response(): ?string
    {
        header('Content-Type: application/json');
        http_response_code(StatusCode::Ok->value);

        return json_encode($this);
    }

    public function jsonSerialize(): mixed
    {
        return $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    public function offsetSet(mixed $offset, mixed $Value): void
    {
        $this->__set($offset, $Value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }
}
