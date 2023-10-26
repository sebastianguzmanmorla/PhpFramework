<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Html\Validation\IValidationRule;

class IsNotNullOrZero implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null
    ) {
        $this->NotValidMessage = $NotValidMessage ?? 'El valor no puede ser nulo o cero';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $Value): bool
    {
        return $Value !== null && $Value !== 0;
    }
}
