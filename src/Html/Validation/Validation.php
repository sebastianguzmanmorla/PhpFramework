<?php

namespace PhpFramework\Html\Validation;

use Closure;
use PhpFramework\Database\DbTable;
use PhpFramework\Html\Markup;

class Validation
{
    public array $Errors = [];

    private array $Rules = [];

    public function __construct(
        private Closure $Value
    ) {
    }

    final public function AddRule(IValidationRule ...$Rules): void
    {
        foreach ($Rules as $Rule) {
            $this->Rules[] = $Rule;
        }
    }

    final public function GetHelpers(): array
    {
        $Helpers = [];

        foreach ($this->Rules as $Rule) {
            if ($Rule->Helper !== null) {
                $Helpers[] = new Markup(Dom: 'p', Class: 'my-1', Content: $Rule->Helper);
            }
        }

        return $Helpers;
    }

    final public function Validate(?DbTable $Context = null): bool
    {
        $this->Errors = [];

        $Value = $this->Value->__invoke();

        $Valid = true;

        foreach ($this->Rules as $Rule) {
            if (!$Rule->Validate($Value, $Context)) {
                $this->Errors[] = new Markup(Dom: 'p', Class: 'my-1', Content: $Rule->NotValidMessage);
                $Valid = false;
            }
        }

        return $Valid;
    }
}
