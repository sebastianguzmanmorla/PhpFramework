<?php

namespace PhpFramework\Html\Validation\Rules;

use LiliDb\Interfaces\IField;
use LiliDb\Interfaces\ITable;
use PhpFramework\Html\Validation\IValidationRule;

class IsNotNull implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?IField &$Field = null
    ) {
        $this->NotValidMessage = $NotValidMessage ?? ($Field?->Label ?? $Field?->Field ?? 'El Valor') . ' no puede ser nulo';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $Value, ?ITable $Table = null): bool
    {
        return $Value !== null;
    }
}
