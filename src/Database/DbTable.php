<?php

namespace PhpFramework\Database;

use ArrayAccess;
use JsonSerializable;
use PhpFramework\Database\Attributes\Table;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Interface\IResponse;

class DbTable extends Table implements ArrayAccess, IResponse, JsonSerializable
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

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }
}
