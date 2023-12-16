<?php

namespace PhpFramework\Html;

use Closure;
use PhpFramework\Html\Validation\IValidationRule;

class FormSelect extends FormInput
{
    public function __construct(
        ?string $Label = null,
        ?string $Class = null,
        ?string $Id = null,
        ?string $Name = null,
        ?string $Placeholder = null,
        Closure|bool|null $Disabled = null,
        Closure|bool|null $ReadOnly = null,
        Closure|array|null $Format = null,
        public array $Options = [],
        mixed $Value = null,
        IValidationRule ...$ValidationRule
    ) {
        parent::__construct(
            Dom: 'select',
            Class: $Class ?? 'form-control select2',
            Label: $Label,
            Id: $Id,
            Name: $Name,
            Placeholder: $Placeholder,
            Disabled: $Disabled,
            ReadOnly: $ReadOnly,
            Format: $Format,
            Type: null,
            Value: $Value
        );

        $this->Validation()->AddRule(...$ValidationRule);
    }

    public function __toString()
    {
        $this->Content = [];

        if ($this->Value instanceof Closure) {
            $this->Value = $this->Value->__invoke();
        }

        foreach ($this->Options as $Value) {
            if (!$Value instanceof FormSelectOption) {
                $Value = new FormSelectOption(
                    Value: $Value['value'],
                    Text: $Value['text'],
                    Selected: $Value['selected'] ?? false,
                );
            }

            if ($Value->Value === $this->Value && !$Value->Selected) {
                $Value->Selected = true;
            }

            $this->Content[] = $Value instanceof Markup ? $Value : new Markup(
                Dom: 'option',
                Value: $Value->Value,
                Content: $Value->Text,
                Selected: $Value->Selected,
            );
        }

        $this->Value = null;

        return parent::__toString();
    }
}
