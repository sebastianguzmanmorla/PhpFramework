<?php

namespace PhpFramework\Html\Validation\Rules;

use LiliDb\Interfaces\IField;
use LiliDb\Interfaces\ITable;
use PhpFramework\Html\Validation\IValidationRule;

class IsBetween implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage,
        ?string $Helper,
        ?IField &$Field,
        public int $Min,
        public int $Max
    ) {
        $this->NotValidMessage = $NotValidMessage ?? ($Field?->Label ?? $Field?->Field ?? 'El Valor') . " debe estar entre {$Min} y {$Max}";
        $this->Helper = $Helper;
    }

    public function Validate(mixed $Value, ?ITable $Table = null): bool
    {
        return $Value >= $this->Min && $Value <= $this->Max;
    }
}
