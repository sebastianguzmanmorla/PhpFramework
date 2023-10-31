<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Html\Validation\IValidationRule;

class IsValidRut implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?Field &$Field = null
    ) {
        $this->NotValidMessage = $NotValidMessage ?? 'El Valor de ' . ($Field?->Label ?? 'Valor') . ' ingresado no es válido';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $value, ?DbTable $Table = null): bool
    {
        if (!preg_match('/^[0-9]+-[0-9kK]{1}/', $value)) {
            return false;
        }
        $rut = explode('-', $value);

        return strtolower($rut[1]) == static::Digito($rut[0]);
    }

    public static function Digito(int $T): string
    {
        $M = 0;
        $S = 1;
        for (; $T; $T = floor($T / 10)) {
            $S = ($S + $T % 10 * (9 - $M++ % 6)) % 11;
        }

        return $S ? $S - 1 : 'k';
    }
}
