<?php

namespace PhpFramework\Database;

use ArrayAccess;
use JsonSerializable;
use ReflectionClass;

class DbTable implements ArrayAccess, JsonSerializable
{
    public function __isset(string $name): bool
    {
        $Reflection = new ReflectionClass($this);

        return $Reflection->hasProperty($name);
    }

    public function __get(string $name): mixed
    {
        $Reflection = new ReflectionClass($this);
        if ($Reflection->hasProperty($name)) {
            $Property = $Reflection->getProperty($name);

            return $Property->getValue($this);
        }

        return null;
    }

    public function __set(string $name, mixed $value): void
    {
        $Reflection = new ReflectionClass($this);
        if ($Reflection->hasProperty($name)) {
            $Property = $Reflection->getProperty($name);
            $Property->setValue($this, $value);
        }
    }

    public function __unset(string $name): void
    {
        $Reflection = new ReflectionClass($this);
        if ($Reflection->hasProperty($name)) {
            $Property = $Reflection->getProperty($name);
            $Property->setValue($this, null);
        }
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

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }
}
