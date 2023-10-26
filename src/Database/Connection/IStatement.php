<?php

namespace PhpFramework\Database\Connection;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbQuery;

interface IStatement
{
    public function __construct(IConnection &$Connection, DbQuery $Query, ?Field $Field = null);

    public function Execute(): bool;

    public function Result(bool $Associative = false): array|false;

    public function InsertId(): int|string|false;

    public function Error(): string;

    public function Close(): void;
}
