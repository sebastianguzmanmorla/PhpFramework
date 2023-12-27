<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Html\Validation\IValidationRule;

class IsBetween implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage,
        ?string $Helper,
        ?Field &$Field,
        public int $Min,
        public int $Max
    ) {
        $this->NotValidMessage = $NotValidMessage ?? ($Field?->Label ?? $Field?->Field ?? 'El Valor') . " debe estar entre {$Min} y {$Max}";
        $this->Helper = $Helper;
    }

    public function Validate(mixed $value, ?DbTable $Table = null): bool
    {
        return $value >= $this->Min && $value <= $this->Max;
    }
}
