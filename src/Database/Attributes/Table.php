<?php

namespace PhpFramework\Database\Attributes;

use Attribute;
use Generator;
use PhpFramework\Database\DbQuery;
use PhpFramework\Database\DbSchema;
use PhpFramework\Database\DbSet;
use PhpFramework\Database\DbTable;
use PhpFramework\Database\DbValue;
use PhpFramework\Database\Enumerations\DbWhere;
use ReflectionClass;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Table
{
    protected ReflectionClass $ReflectionClass;

    protected ReflectionProperty $ReflectionProperty;

    protected DbSchema $Schema;

    protected DbSet $DbSet;

    public function __construct(
        protected string $Class,
        protected string $Name
    ) {
        $this->ReflectionClass = new ReflectionClass($Class);
    }

    public function __toString()
    {
        return '`' . $this->Schema . '`.`' . $this->Name . '`';
    }

    public function Initialize(
        DbSchema &$DbSchema,
        DbSet &$DbSet,
        ReflectionProperty &$ReflectionProperty
    ): void {
        $this->Schema = $DbSchema;
        $this->DbSet = $DbSet;
        $this->ReflectionProperty = $ReflectionProperty;
    }

    public function DbSet(): DbSet
    {
        return $this->DbSet;
    }

    public function newInstance(): DbTable
    {
        $DbTable = $this->ReflectionClass->newInstance();
        $DbTable->ReflectionClass = &$this->ReflectionClass;
        $DbTable->Class = $this->Class;
        $DbTable->Name = $this->Name;

        return $DbTable;
    }

    public function getShortName(): string
    {
        return $this->ReflectionClass->getShortName();
    }

    public function InitializeFields(DbSchema &$DbSchema, DbTable &$DbTable): void
    {
        foreach ($this->ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $DbTableProperty) {
            $Field = $DbTableProperty->getAttributes(Field::class);
            $Field = isset($Field[0]) ? $Field[0]->newInstance() : null;

            if ($Field !== null) {
                $Field->Initialize($DbSchema->Schema, $DbTable, $DbTableProperty);

                $DbTableProperty->setValue($DbTable, $Field);
            }
        }
    }

    public function CreateSyntax(): DbQuery
    {
        $Fields = iterator_to_array($this->Fields());
        $PrimaryKeys = iterator_to_array($this->GetPrimaryKeys());

        return new DbQuery(Query: ["CREATE TABLE IF NOT EXISTS {$this} ("
            . implode(', ', array_map(fn (Field $Field) => "`{$Field->Field}` "
                . ($Field->Type != null ? $Field->Type->value : '')
                . ($Field->FieldLength !== null ? "({$Field->FieldLength})" : '')
                . ($Field->AllowNull ? '' : ' NOT NULL')
                . ($Field->AutoIncrement ? ' AUTO_INCREMENT' : '')
                . ($Field->Default !== null ? " DEFAULT '{$Field->Default}'" : ''), $Fields))
            . (empty($PrimaryKeys) ? '' : ', PRIMARY KEY (`' . implode('`, `', array_map(fn (Field $Field) => $Field->Field, $PrimaryKeys)) . '`)')
            . ');']);
    }

    public function Field(string $Name): ?Field
    {
        return $this->ReflectionClass->hasProperty($Name) ? $this->ReflectionClass->getProperty($Name)->getValue($this) : null;
    }

    public function Fields(): Generator
    {
        foreach ($this->ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Field = $Property->getValue($this);

            if ($Field instanceof Field) {
                yield $Field;
            }
        }
    }

    public function PrepareSet(self &$Table): Generator
    {
        foreach ($this->Fields() as $Field) {
            $Value = $Field->GetValue($Table);
            if ($Value !== null) {
                yield new DbValue($Field, Where: DbWhere::Equal, Value: $Value, IsUpdateSet: true);
            }
        }
    }

    public function GetPrimaryKeys(): Generator
    {
        foreach ($this->Fields() as $Field) {
            if ($Field->PrimaryKey) {
                yield $Field;
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

    public function SetPrimaryKeyValue(self &$Table, mixed $Value): void
    {
        $PrimaryKey = $this->GetPrimaryKey();

        if ($PrimaryKey != null) {
            $PrimaryKey->SetValue($Table, $Value);
        }
    }
}
