<?php

namespace PhpFramework\Html\Validation\Rules;

use LiliDb\Interfaces\IField;
use LiliDb\Interfaces\ITable;
use PhpFramework\Html\Validation\IValidationRule;

class IsValidPassword implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?IField &$Field = null,
    ) {
        $this->NotValidMessage = $NotValidMessage ?? 'La Contraseña ingresada no es válida';
        $this->Helper = $Helper ?? 'La Contraseña debe tener entre 6 y 20 caracteres, al menos una letra mayúscula y un número';
    }

    public function Validate(mixed $Value, ?ITable $Table = null): bool
    {
        if ($Value === null) {
            return false;
        }

        return preg_match('^(?=.*[A-Z])(?=.*\\d)[A-Za-z\\d@$!%*#?&]{6,20}$^', $Value);
    }
}
