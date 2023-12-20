<?php

namespace PhpFramework\Database;

use ArrayAccess;
use JsonSerializable;
use PhpFramework\Database\Attributes\Table;

class DbTable extends Table implements ArrayAccess, JsonSerializable
{
    public function __isset(string $name): bool
    {
        return $this->ReflectionClass->hasProperty($name);
    }

    public function __get(string $name): mixed
    {
        if ($this->ReflectionClass->hasProperty($name)) {
            $Property = $this->ReflectionClass->getProperty($name);

            return $Property->getValue($this);
        }

        return null;
    }

    public function __set(string $name, mixed $value): void
    {
        if ($this->ReflectionClass->hasProperty($name)) {
            $Property = $this->ReflectionClass->getProperty($name);
            $Property->setValue($this, $value);
        }
    }

    public function __unset(string $name): void
    {
        if ($this->ReflectionClass->hasProperty($name)) {
            $Property = $this->ReflectionClass->getProperty($name);
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
