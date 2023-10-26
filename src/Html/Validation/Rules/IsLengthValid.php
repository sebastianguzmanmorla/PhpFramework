<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Html\Validation\IValidationRule;

class IsLengthValid implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        public int $Min = 0,
        public int $Max = 255
    ) {
        $this->NotValidMessage = $NotValidMessage ?? "El valor debe tener entre {$Min} y {$Max} caracteres";
        $this->Helper = $Helper;
    }

    public function Validate(mixed $value): bool
    {
        return strlen($value) >= $this->Min && strlen($value) <= $this->Max;
    }
}
