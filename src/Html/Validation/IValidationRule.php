<?php

namespace PhpFramework\Html\Validation;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;

interface IValidationRule
{
    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?Field &$Field = null
    );

    public function Validate(mixed $Value, ?DbTable $Table = null): bool;
}
