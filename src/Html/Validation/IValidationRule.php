<?php

namespace PhpFramework\Html\Validation;

use LiliDb\Interfaces\IField;
use LiliDb\Interfaces\ITable;

interface IValidationRule
{
    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?IField &$Field = null
    );

    public function Validate(mixed $Value, ?ITable $Table = null): bool;
}
