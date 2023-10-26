<?php

namespace PhpFramework\Html\Validation\Rules;

use PhpFramework\Html\Validation\IValidationRule;

class IsBetween implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage,
        ?string $Helper,
        public int $Min,
        public int $Max
    ) {
        $this->NotValidMessage = $NotValidMessage ?? "El valor debe estar entre {$Min} y {$Max}";
        $this->Helper = $Helper;
    }

    public function Validate(mixed $value): bool
    {
        return $value >= $this->Min && $value <= $this->Max;
    }
}
