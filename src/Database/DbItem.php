<?php

namespace PhpFramework\Database;

use ArrayAccess;
use Exception;
use JsonSerializable;
use ReflectionClass;
use ReflectionObject;
use stdClass;

class DbItem extends stdClass implements ArrayAccess, JsonSerializable
{
    public function jsonSerialize(): mixed
    {
        return $this;
    }

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

        return throw new Exception("Property {$name} not found");
    }

    public function __set(string $name, mixed $value): void
    {
        $Reflection = new ReflectionObject($this);

        if ($value instanceof DbTable || $Reflection->name == self::class) {
            $this->{$name} = $value;
        } else {
            if ($Reflection->hasProperty($name)) {
                $Reflection->getProperty($name)->setValue($this, $value);
            } else {
                $Properties = $Reflection->getProperties();

                foreach ($Properties as $Property) {
                    $Table = $Property->getValue($this);

                    if ($Table instanceof DbTable) {
                        $TableReflection = new ReflectionClass($Table);

                        if ($TableReflection->hasProperty($name)) {
                            $TableReflection->getProperty($name)->setValue($Table, $value);
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

    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }
}
