<?php

namespace PhpFramework\Database;

use Exception;
use PhpFramework\Database\Attributes\Schema;
use PhpFramework\Database\Attributes\Table;
use PhpFramework\Database\Connection\IConnection;
use ReflectionClass;
use ReflectionProperty;
use Throwable;

class DbSchema
{
    public readonly IConnection $Connection;

    public DbQuery $Query;

    public $locked = false;

    private bool $Connected = false;

    public ?Schema $Schema = null;

    /**
     * @var DbTable[]
     */
    public array $Tables = [];

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

    public function __construct(
        IConnection $Connection
    ) {
        $this->Connection = $Connection;

        $ReflectionClass = new ReflectionClass($this);

        $Schema = $ReflectionClass->getAttributes(Schema::class);

        $this->Schema = isset($Schema[0]) ? $Schema[0]->newInstance() : null;

        if ($this->Schema === null) {
            throw new Exception('Schema Attribute not found at class ' . $this::class);
        }

        foreach ($ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Table = $Property->getAttributes(Table::class);
            $Table = isset($Table[0]) ? $Table[0]->newInstance() : null;

            if ($Table !== null) {
                $Table->DbSchema = &$this;

                $DbSet = new DbSet($this, $Table);

                $Property->setValue($this, $DbSet);

                $this->Tables[$Table->DbTable::class] = $Table;
            }
        }
    }

    public function Execute(DbQuery $Query)
    {
        $this->Query = $Query;

        if ($this->Connection() === false || $this->locked) {
            return new DbResourceSet();
        }

        if ($Bind = $this->Connection()->Prepare($this->Query)) {
            if ($Bind->Execute()) {
                $Data = $Bind->Result(true);
                if ($Data !== null) {
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
