<?php

namespace PhpFramework\Database;

use Exception;
use Generator;
use PhpFramework\Database\Attributes\Schema;
use PhpFramework\Database\Attributes\Table;
use PhpFramework\Database\Connection\IConnection;
use ReflectionProperty;
use Throwable;

abstract class DbSchema extends Schema
{
    public bool $Locked = false;

    public self $Schema;

    public ?DbQuery $Query = null;

    private readonly IConnection $Connection;

    private bool $Connected = false;

    public function __toString()
    {
        return $this->Name;
    }

    public static function Initialize(
        IConnection $Connection
    ): static {
        $DbSchema = new static();

        $DbSchema->InitializeReflection($DbSchema::class);

        $Schema = $DbSchema->ReflectionClass->getAttributes(Schema::class);

        if (!isset($Schema[0])) {
            throw new Exception('Schema Attribute not found at class ' . $DbSchema::class);
        }

        $Schema = $Schema[0]->newInstance();

        $DbSchema->Name = $Schema->Name;

        $DbSchema->Schema = clone $DbSchema;

        $DbSchema->Schema->Schema = &$DbSchema->Schema;

        foreach ($DbSchema->ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $DbSchemaProperty) {
            $Table = $DbSchemaProperty->getAttributes(Table::class);
            $Table = isset($Table[0]) ? $Table[0]->newInstance() : null;

            if ($Table !== null) {
                $DbSet = new DbSet();

                $DbTable = $Table->newInstance();

                $DbTable->Initialize($DbSchema->Schema, $DbSet, $DbSchemaProperty);

                $Table->InitializeFields($DbSchema, $DbTable);

                $DbSet->Initialize($DbSchema, $DbTable);

                $DbSchemaProperty->setValue($DbSchema, $DbSet);

                $DbSchemaProperty->setValue($DbSchema->Schema, $DbTable);
            }
        }

        $DbSchema->Connection = $Connection;

        return $DbSchema;
    }

    public function Connection(): IConnection
    {
        if ($this->Connected == false) {
            try {
                $this->Connected = $this->Connection->Connect();
            } catch (Throwable $e) {
                $this->Connected = false;

                throw new Exception('Database connection failed: ' . $e->getMessage());
            }
        }

        return $this->Connection;
    }

    public function Tables(): Generator
    {
        foreach ($this->ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Table = $Property->getValue($this->Schema);

            if ($Table instanceof DbTable) {
                yield $Table;
            }
        }
    }

    public function TableByClass(string $ClassName): ?DbTable
    {
        foreach ($this->ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Table = $Property->getValue($this->Schema);

            if ($Table instanceof DbTable && $Table::class == $ClassName) {
                return $Table;
            }
        }

        return null;
    }

    public function Execute(DbQuery $Query)
    {
        $this->Query = $Query;

        if ($this->Connection() === false || $this->Locked) {
            return new DbResourceSet();
        }

        if ($Bind = $this->Connection()->Prepare($this->Query)) {
            if ($Bind->Execute()) {
                $Data = $Bind->Result(true);
                if ($Data !== null && $Data !== false) {
                    $Result = [];

                    foreach ($Data as $Row) {
                        $Item = new DbItem();

                        foreach ($Row as $Name => $Value) {
                            $Item->__set($Name, $Value);
                        }

                        $Result[] = $Item;
                    }

                    $DbResourceSet = new DbResourceSet($Result);
                    $DbResourceSet->Query = $this->Query;

                    $Bind->Close();

                    return $DbResourceSet;
                }

                return $Bind->Error() == 0;
            }

            throw new Exception($Bind->Error());
        } else {
            throw new Exception($this->Connection()->Error());
        }
    }
}
