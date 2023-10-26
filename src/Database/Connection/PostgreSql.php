<?php

namespace PhpFramework\Database\Connection;

use PgSql\Connection;
use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbQuery;
use SensitiveParameter;

class PostgreSql implements IConnection
{
    public Connection|false $Client;

    public function __construct(
        #[SensitiveParameter]
        public ?string $Hostname,
        #[SensitiveParameter]
        public ?string $Database,
        #[SensitiveParameter]
        public ?string $Username,
        #[SensitiveParameter]
        public ?string $Password
    ) {
        $this->Client = pg_connect("host={$this->Hostname} dbname={$this->Database} user={$this->Username} password={$this->Password}");
        pg_set_client_encoding($this->Client, 'utf8');
    }

    public function Connect(): bool
    {
        return pg_connection_status($this->Client) === PGSQL_CONNECTION_OK;
    }

    public function Prepare(DbQuery &$Query, ?Field $Field = null): IStatement
    {
        return new PostgreSqlStatement($this, $Query, $Field);
    }

    public function Error(): string
    {
        return pg_last_error($this->Client);
    }

    public function Close(): void
    {
        pg_close($this->Client);
    }
}
