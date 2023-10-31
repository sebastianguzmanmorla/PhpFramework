<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Html\Validation\IValidationRule;

class IsNotNullOrZero implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?Field &$Field = null
    ) {
        $this->NotValidMessage = $NotValidMessage ?? 'El Valor de ' . ($Field?->Label ?? 'Valor') . ' no puede ser nulo o cero';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $Value, ?DbTable $Table = null): bool
    {
        return $Value !== null && $Value !== 0;
    }
}
