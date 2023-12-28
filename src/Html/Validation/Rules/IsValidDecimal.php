<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Html\Validation\IValidationRule;

class IsValidDecimal implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        ?Field &$Field = null,
        public int $NumericoMax = 8,
        public int $DecimalMax = 2
    ) {
        $this->NotValidMessage = $NotValidMessage ?? ($Field?->Label ?? $Field?->Field ?? 'El Valor') . ' no es un decimal válido';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $Value, ?DbTable $Table = null): bool
    {
        return preg_match(static::RegexDecimal($this->NumericoMax, $this->DecimalMax), $Value);
    }

    public static function RegexDecimal(int $NumericoMax, int $DecimalMax, bool $Negativo = false): string
    {
        return '^' . ($Negativo ? '-?' : '') . '[\\d]{1,' . $NumericoMax . '}(?:[.,][\\d]{0,' . $DecimalMax . '})?';
    }
}
