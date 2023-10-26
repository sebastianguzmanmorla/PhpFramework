<?php

namespace PhpFramework\Database\Connection;

use DateTime;
use PgSql\Result;
use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbQuery;

class PostgreSqlStatement implements IStatement
{
    private string $GUID;

    private PostgreSql $Connection;

    private Result|false $Statement;

    private DbQuery $Query;

    private ?Field $Field;

    private int $ParameterMarkerCount = 1;

    public function __construct(IConnection &$Connection, DbQuery $Query, ?Field $Field = null)
    {
        $this->Connection = $Connection;
        $this->Query = $Query;
        $this->Field = $Field;

        $this->GUID = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

        if ($Query->Limit !== null) {
            $Query->Query[] = 'LIMIT ' . $Query->Limit;
        }

        if ($Query->Offset !== null) {
            $Query->Query[] = 'OFFSET ' . $Query->Offset;
        }

        $Sql = $Query->ToSql(fn () => '$' . $this->ParameterMarkerCount++, true);

        if ($this->Field !== null) {
            $Sql .= ' RETURNING ' . $Field->Field;
        }

        $this->Statement = @pg_prepare($this->Connection->Client, $this->GUID, $Sql);

        if (count($Query->Parameters) > 0) {
            foreach ($Query->Parameters as $k => &$v) {
                if ($v instanceof DateTime) {
                    $v = $v->format('Y-m-d H:i:s');
                }
            }
        }
    }

    public function Execute(): bool
    {
        if ($this->Statement === false) {
            return false;
        }

        $this->Statement = pg_execute($this->Connection->Client, $this->GUID, $this->Query->Parameters);

        return $this->Statement !== false;
    }

    public function Result(bool $Associative = false): array|false
    {
        if ($this->Statement === false) {
            return false;
        }

        return pg_fetch_all($this->Statement, $Associative ? PGSQL_ASSOC : PGSQL_NUM);
    }

    public function InsertId(): int|string|false
    {
        if ($this->Statement === false) {
            return false;
        }
        $Row = pg_fetch_row($this->Statement);

        return $Row[0] ?? false;
    }

    public function Error(): string
    {
        return $this->Statement === false ? pg_last_error($this->Connection->Client) : pg_result_error($this->Statement);
    }

    public function Close(): void
    {
        if ($this->Statement !== false) {
            pg_free_result($this->Statement);
        }
    }
}
