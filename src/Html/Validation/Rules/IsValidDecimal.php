<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Html\Validation\IValidationRule;

class IsValidDecimal implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        public int $NumericoMax = 8,
        public int $DecimalMax = 2
    ) {
        $this->NotValidMessage = $NotValidMessage ?? 'El valor no es un decimal válido';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $value): bool
    {
        return preg_match(static::RegexDecimal($this->NumericoMax, $this->DecimalMax), $value);
    }

    public static function RegexDecimal(int $NumericoMax, int $DecimalMax, bool $Negativo = false): string
    {
        return '^' . ($Negativo ? '-?' : '') . '[\\d]{1,' . $NumericoMax . '}(?:[.,][\\d]{0,' . $DecimalMax . '})?';
    }
}
