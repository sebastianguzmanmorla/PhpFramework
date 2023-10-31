<?php

namespace PhpFramework\Database\Attributes;

use Attribute;
use Generator;
use PhpFramework\Database\DbSchema;
use PhpFramework\Database\DbTable;
use PhpFramework\Database\DbValue;
use PhpFramework\Database\Enumerations\DbWhere;
use ReflectionClass;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Table
{
    public DbSchema $DbSchema;

    public DbTable $DbTable;

    public ReflectionClass $Reflection;

    public function __construct(
        string $Class,
        public string $Name
    ) {
        $this->Reflection = new ReflectionClass($Class);

        $this->DbTable = $this->Reflection->newInstance();

        foreach ($this->Reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Field = $Property->getAttributes(Field::class);
            $Field = isset($Field[0]) ? $Field[0]->newInstance() : null;

            if ($Field !== null) {
                $Field->Reflection = $Property;
                $Field->Table = &$this;
                $Property->setValue($this->DbTable, $Field);
            }
        }
    }

    public function Field(string $Name): ?Field
    {
        return $this->Reflection->hasProperty($Name) ? $this->Reflection->getProperty($Name)->getValue($this->DbTable) : null;
    }

    public function Fields(): Generator
    {
        foreach ($this->Reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Field = $Property->getValue($this->DbTable);

            if ($Field instanceof Field) {
                yield $Field;
            }
        }
    }

    public function PrepareSet(DbTable &$Table): Generator
    {
        foreach ($this->Fields() as $Field) {
            $Value = $Field->Reflection->getValue($Table);
            if ($Value !== null) {
                yield new DbValue($Field, Where: DbWhere::Equal, Value: $Value, IsUpdateSet: true);
            }
        }
    }

    public function GetPrimaryKey(): ?Field
    {
        foreach ($this->Fields() as $Field) {
            if ($Field->PrimaryKey) {
                return $Field;
            }
        }

        return null;
    }

    public function GetFilters(): Generator
    {
        foreach ($this->Fields() as $Field) {
            if ($Field->Filter !== null) {
                yield $Field;
            }
        }
    }

    public function SetPrimaryKey(DbTable &$Table, int $id): void
    {
        $PrimaryKey = $this->GetPrimaryKey();

        if ($PrimaryKey !== null) {
            $PrimaryKey->Reflection->setValue($Table, $id);
        }
    }

    public function __toString()
    {
        return $this->DbSchema->Schema->__toString() . '.' . $this->Name;
    }
}
