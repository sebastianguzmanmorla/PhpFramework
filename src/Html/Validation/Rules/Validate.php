<?php

namespace PhpFramework\Html\Validation\Rules;

use Closure;
use PhpFramework\Html\Validation\IValidationRule;

class Validate implements IValidationRule
{
    public string $NotValidMessage;

    public ?string $Helper;

    public function __construct(
        ?string $NotValidMessage = null,
        ?string $Helper = null,
        public ?Closure $Validation = null
    ) {
        $this->NotValidMessage = $NotValidMessage ?? 'El valor no es válido';
        $this->Helper = $Helper;
    }

    public function Validate(mixed $value): bool
    {
        return $this->Validation->__invoke($value);
    }
}
