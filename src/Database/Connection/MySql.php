<?php

namespace PhpFramework\Database\Connection;

use mysqli;
use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbQuery;
use SensitiveParameter;

class MySql implements IConnection
{
    public mysqli|false $Client;

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
        $this->Client = mysqli_init();
        $this->Client->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    }

    public function Connect(): bool
    {
        return $this->Client->real_connect($this->Hostname, $this->Username, $this->Password, $this->Database);
    }

    public function Prepare(DbQuery &$Query, ?Field $Field = null): IStatement
    {
        return new MySqlStatement($this, $Query, $Field);
    }

    public function Error(): string
    {
        return $this->Client->error;
    }

    public function Close(): void
    {
        $this->Client->close();
    }
}
