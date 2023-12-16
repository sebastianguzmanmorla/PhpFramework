<?php

namespace PhpFramework\Database\Connection;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbQuery;
use SensitiveParameter;

interface IConnection
{
    public function __construct(
        #[SensitiveParameter]
        ?string $Hostname,
        #[SensitiveParameter]
        ?int $Port,
        #[SensitiveParameter]
        ?string $Database,
        #[SensitiveParameter]
        ?string $Username,
        #[SensitiveParameter]
        ?string $Password
    );

    public function Connect(): bool;

    public function Prepare(DbQuery &$Query, ?Field $Field = null): IStatement;

    public function Error(): string;

    public function Close(): void;
}
