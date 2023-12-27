<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Html\Validation\IValidationRule;

class IsValidEmail implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?Field &$Field = null
    ) {
        $this->NotValidMessage = $NotValidMessage ?? ($Field?->Label ?? $Field?->Field ?? 'El Valor') . ' debe ser un email válido';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $value, ?DbTable $Table = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
