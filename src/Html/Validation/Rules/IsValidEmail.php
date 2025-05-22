<?php

namespace PhpFramework\Html\Validation\Rules;

use LiliDb\Interfaces\IField;
use LiliDb\Interfaces\ITable;
use PhpFramework\Html\Validation\IValidationRule;

class IsValidEmail implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?IField &$Field = null
    ) {
        $this->NotValidMessage = $NotValidMessage ?? ($Field?->Label ?? $Field?->Field ?? 'El Valor') . ' debe ser un correo válido';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $Value, ?ITable $Table = null): bool
    {
        return filter_var($Value, FILTER_VALIDATE_EMAIL);
    }
}
