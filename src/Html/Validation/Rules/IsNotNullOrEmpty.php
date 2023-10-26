<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Html\Validation\IValidationRule;

class IsNotNullOrEmpty implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null
    ) {
        $this->NotValidMessage = $NotValidMessage ?? 'El valor no puede ser nulo o vacío';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $value): bool
    {
        return $value !== null && $value !== '';
    }
}
