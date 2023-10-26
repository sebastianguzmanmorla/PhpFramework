<?php

namespace PhpFramework\Database\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Schema
{
    public function __construct(
        public ?string $Name = null
    ) {
    }

    public function __toString()
    {
        return $this->Name;
    }
}
