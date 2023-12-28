<?php

namespace PhpFramework\Attributes;

use Attribute;
use PhpFramework\Html\Validation\IValidationRule;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Validation
{
    public array $Errors = [];

    /**
     * @var IValidationRule[]
     */
    private array $Rules = [];

    public function __construct(
        IValidationRule ...$ValidationRule
    ) {
        $this->Rules = $ValidationRule;
    }

    public function Validate(mixed $Value, ?object $Context = null): bool
    {
        $this->Errors = [];

        $Valid = true;

        foreach ($this->Rules as $Rule) {
            if (!$Rule->Validate($Value, $Context)) {
                $this->Errors[] = $Rule->NotValidMessage;
                $Valid = false;
            }
        }

        return $Valid;
    }
}
