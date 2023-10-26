<?php

namespace PhpFramework\Html\Validation;

interface IValidationRule
{
    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null
    );

    public function Validate(mixed $value): bool;
}
