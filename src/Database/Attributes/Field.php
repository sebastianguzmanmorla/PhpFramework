<?php

namespace PhpFramework\Database\Attributes;

use Attribute;
use PhpFramework\Database\Enumerations\DbType;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    public ReflectionProperty $Reflection;

    public ?Table $Table;

    public function __construct(
        public ?DbType $Type = null,
        public ?int $MinLength = null,
        public ?int $MaxLength = null,
        public ?int $DecimalLength = null,
        public ?string $Field = null,
        public ?string $Label = null,
        public bool $PrimaryKey = false,
        public bool $AutoIncrement = false,
        public bool $NotNull = false
    ) {
    }

    public function __toString()
    {
        return ($this->Table !== null ? $this->Table . '.' : '') . $this->Field;
    }
}
