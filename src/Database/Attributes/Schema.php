<?php

namespace PhpFramework\Database\Attributes;

use Attribute;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
class Schema
{
    protected ReflectionClass $ReflectionClass;

    public function __construct(
        public ?string $Name = null
    ) {
    }

    public function __toString(): string
    {
        return $this->Name;
    }

    protected function InitializeReflection(
        string $Class
    ): void {
        $this->ReflectionClass = new ReflectionClass($Class);
    }
}
