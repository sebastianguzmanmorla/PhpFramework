<?php

namespace PhpFramework\Html\Validation\Rules;

use LiliDb\Interfaces\IField;
use LiliDb\Interfaces\ITable;
use PhpFramework\Html\Validation\IValidationRule;

class IsLengthValid implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?IField &$Field = null,
        public int $Min = 0,
        public int $Max = 255,
    ) {
        if ($Field != null) {
            $this->Min = $Field->MinLength ?? 0;
            $this->Max = $Field->MaxLength ?? 255;
            $this->NotValidMessage = $NotValidMessage ?? ($Field->Label ?? $Field->Field ?? 'El Valor') . " debe tener entre {$this->Min} y {$this->Max} caracteres";
        } else {
            $this->NotValidMessage = $NotValidMessage ?? "El Valor debe tener entre {$Min} y {$Max} caracteres";
        }

        $this->Helper = $Helper;
    }

    public function Validate(mixed $Value, ?ITable $Table = null): bool
    {
        return $Value !== null && strlen($Value) >= $this->Min && strlen($Value) <= $this->Max;
    }
}
