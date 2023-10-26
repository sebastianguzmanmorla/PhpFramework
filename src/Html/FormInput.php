<?php

namespace PhpFramework\Html;

use Closure;
use Exception;
use PhpFramework\Html\Enums\InputType;
use PhpFramework\Html\Validation\IValidation;
use PhpFramework\Html\Validation\IValidationRule;
use PhpFramework\Html\Validation\Validation;

class FormInput extends Html implements IValidation
{
    private Validation $Validation;

    public function Validation(): Validation
    {
        return $this->Validation;
    }

    public const Default = [self::class, 'Default'];
    public const DefaultStatic = [self::class, 'DefaultStatic'];
    public const Floating = [self::class, 'Floating'];
    public const Inline = [self::class, 'Inline'];
    public const InlineStatic = [self::class, 'InlineStatic'];
    public const InlineGroup = [self::class, 'InlineGroup'];

    public static function Default(): Closure
    {
        return static fn (FormInput $Context) => new Html(
            Dom: 'div',
            Class: 'mb-2',
            Content: [
                new Html(
                    Dom: 'label',
                    Class: 'form-label',
                    Content: $Context->Label,
                    For: $Context->Name
                ),
                new Html(
                    Dom: $Context->Dom ?? 'input',
                    Type: $Context->Type,
                    Max: $Context->Max,
                    MaxLength: $Context->MaxLength,
                    Content: $Context->Content,
                    Disabled: $Context->Disabled,
                    ReadOnly: $Context->ReadOnly,
                    Class: ($Context->Class ?? '') . (count($Context->Validation->Errors) > 0 ? ' is-invalid' : ''),
                    Style: $Context->Style,
                    Id: $Context->Id,
                    Name: $Context->Name,
                    Value: $Context->Value
                ),
                $Context->Helper !== null ? new Html(
                    Dom: 'div',
                    Class: 'form-text',
                    Content: $Context->Helper
                ) : false,
                fn () => !empty($Context->Validation->GetHelpers()) ? new Html(
                    Dom: 'div',
                    Class: 'form-text',
                    Content: $Context->Validation->GetHelpers()
                ) : false,
                fn () => !empty($Context->Validation->Errors) ? new Html(
                    Dom: 'div',
                    Class: 'invalid-feedback',
                    Content: $Context->Validation->Errors
                ) : false,
            ]
        );
    }

    public static function DefaultStatic(): Closure
    {
        return static fn (FormInput $Context) => new Html(
            Dom: 'div',
            Class: 'mb-2',
            Content: [
                new Html(
                    Dom: 'label',
                    Class: 'form-label',
                    Content: $Context->Label,
                    For: $Context->Name
                ),
                new Html(
                    Dom: $Context->Dom ?? 'input',
                    Type: $Context->Type,
                    Max: $Context->Max,
                    MaxLength: $Context->MaxLength,
                    Content: $Context->Content,
                    Disabled: $Context->Disabled ?? true,
                    ReadOnly: $Context->ReadOnly ?? true,
                    Class: 'form-control-plaintext' . (count($Context->Validation->Errors) > 0 ? ' is-invalid' : ''),
                    Style: $Context->Style,
                    Id: $Context->Id,
                    Name: $Context->Name,
                    Value: $Context->Value
                ),
                $Context->Helper !== null ? new Html(
                    Dom: 'div',
                    Class: 'form-text',
                    Content: $Context->Helper
                ) : false,
                fn () => !empty($Context->Validation->GetHelpers()) ? new Html(
                    Dom: 'div',
                    Class: 'form-text',
                    Content: $Context->Validation->GetHelpers()
                ) : false,
                fn () => !empty($Context->Validation->Errors) ? new Html(
                    Dom: 'div',
                    Class: 'invalid-feedback',
                    Content: $Context->Validation->Errors
                ) : false,
            ]
        );
    }

    public static function Floating(): Closure
    {
        return static fn (FormInput $Context) => new Html(
            Dom: 'div',
            Class: 'form-floating',
            Content: [
                new Html(
                    Dom: $Context->Dom ?? 'input',
                    Type: $Context->Type,
                    Max: $Context->Max,
                    MaxLength: $Context->MaxLength,
                    Content: $Context->Content,
                    Disabled: $Context->Disabled,
                    ReadOnly: $Context->ReadOnly,
                    Class: ($Context->Class ?? '') . (count($Context->Validation->Errors) > 0 ? ' is-invalid' : ''),
                    Style: $Context->Style,
                    Id: $Context->Id,
                    Name: $Context->Name,
                    Value: $Context->Value,
                    PlaceHolder: $Context->Placeholder
                ),
                new Html(
                    Dom: 'label',
                    Content: $Context->Label,
                    For: $Context->Id
                ),
                $Context->Helper !== null ? new Html(
                    Dom: 'div',
                    Class: 'form-text',
                    Content: $Context->Helper
                ) : false,
                fn () => !empty($Context->Validation->GetHelpers()) ? new Html(
                    Dom: 'div',
                    Class: 'form-text',
                    Content: $Context->Validation->GetHelpers()
                ) : false,
                fn () => !empty($Context->Validation->Errors) ? new Html(
                    Dom: 'div',
                    Class: 'invalid-feedback',
                    Content: $Context->Validation->Errors
                ) : false,
            ]
        );
    }

    public static function Inline(): Closure
    {
        return static fn (FormInput $Context) => new Html(
            Dom: 'div',
            Class: 'mb-2 row',
            Content: [
                new Html(
                    Dom: 'label',
                    Class: 'col-sm-2 col-form-label',
                    Content: $Context->Label,
                    For: $Context->Id,
                    Id: $Context->Id . '_label' ?? $Context->Name . '_label'
                ),
                new Html(
                    Dom: 'div',
                    Class: 'col-sm-10',
                    Content: [
                        new Html(
                            Dom: $Context->Dom ?? 'input',
                            Type: $Context->Type,
                            Max: $Context->Max,
                            MaxLength: $Context->MaxLength,
                            Content: $Context->Content,
                            Disabled: $Context->Disabled,
                            ReadOnly: $Context->ReadOnly,
                            Class: ($Context->Class ?? '') . (count($Context->Validation->Errors) > 0 ? ' is-invalid' : ''),
                            Style: $Context->Style,
                            Id: $Context->Id,
                            Name: $Context->Name,
                            Value: $Context->Value,
                            PlaceHolder: $Context->Placeholder,
                            AriaLabel: $Context->Placeholder,
                            AriaDescribedBy: $Context->Id . '_label' ?? $Context->Name . '_label'
                        ),
                        $Context->Helper !== null ? new Html(
                            Dom: 'div',
                            Class: 'form-text',
                            Content: $Context->Helper
                        ) : false,
                        fn () => !empty($Context->Validation->GetHelpers()) ? new Html(
                            Dom: 'div',
                            Class: 'form-text',
                            Content: $Context->Validation->GetHelpers()
                        ) : false,
                        fn () => !empty($Context->Validation->Errors) ? new Html(
                            Dom: 'div',
                            Class: 'invalid-feedback',
                            Content: $Context->Validation->Errors
                        ) : false,
                    ]
                ),
            ]
        );
    }

    public static function InlineStatic(): Closure
    {
        return static fn (FormInput $Context) => new Html(
            Dom: 'div',
            Class: 'mb-2 row',
            Content: [
                new Html(
                    Dom: 'label',
                    Class: 'col-sm-2 col-form-label',
                    Content: $Context->Label,
                    For: $Context->Id,
                    Id: $Context->Id . '_label' ?? $Context->Name . '_label'
                ),
                new Html(
                    Dom: 'div',
                    Class: 'col-sm-10',
                    Content: [
                        new Html(
                            Dom: $Context->Dom ?? 'input',
                            Type: $Context->Type,
                            Max: $Context->Max,
                            MaxLength: $Context->MaxLength,
                            Content: $Context->Content,
                            Disabled: $Context->Disabled ?? true,
                            ReadOnly: $Context->ReadOnly ?? true,
                            Class: 'form-control-plaintext' . (count($Context->Validation->Errors) > 0 ? ' is-invalid' : ''),
                            Style: $Context->Style,
                            Id: $Context->Id,
                            Name: $Context->Name,
                            Value: $Context->Value,
                            PlaceHolder: $Context->Placeholder,
                            AriaLabel: $Context->Placeholder,
                            AriaDescribedBy: $Context->Id . '_label' ?? $Context->Name . '_label'
                        ),
                        $Context->Helper !== null ? new Html(
                            Dom: 'div',
                            Class: 'form-text',
                            Content: $Context->Helper
                        ) : false,
                        fn () => !empty($Context->Validation->GetHelpers()) ? new Html(
                            Dom: 'div',
                            Class: 'form-text',
                            Content: $Context->Validation->GetHelpers()
                        ) : false,
                        fn () => !empty($Context->Validation->Errors) ? new Html(
                            Dom: 'div',
                            Class: 'invalid-feedback',
                            Content: $Context->Validation->Errors
                        ) : false,
                    ]
                ),
            ]
        );
    }

    public static function InlineGroup(): Closure
    {
        return static fn (FormInput $Context) => new Html(
            Dom: 'div',
            Class: 'input-group mb-2 row',
            Content: [
                new Html(
                    Dom: 'label',
                    Class: 'col-auto input-group-text',
                    Content: $Context->Label ?? false,
                    For: $Context->Id ?? false,
                    Id: $Context->Id . '_label' ?? $Context->Name . '_label' ?? false
                ),
                new Html(
                    Dom: $Context->Dom ?? 'input',
                    Type: $Context->Type,
                    Max: $Context->Max,
                    MaxLength: $Context->MaxLength,
                    Content: $Context->Content ?? false,
                    Disabled: $Context->Disabled ?? false,
                    ReadOnly: $Context->ReadOnly ?? false,
                    Class: 'col-auto ' . ($Context->Class ?? '') . (count($Context->Validation->Errors) > 0 ? ' is-invalid' : ''),
                    Style: $Context->Style ?? false,
                    Id: $Context->Id ?? false,
                    Name: $Context->Name ?? false,
                    Value: $Context->Value ?? false,
                    PlaceHolder: $Context->Placeholder ?? false,
                    AriaLabel: $Context->Placeholder ?? false,
                    AriaDescribedBy: $Context->Id . '_label' ?? $Context->Name . '_label' ?? false
                ),
                $Context->Helper !== null ? new Html(
                    Dom: 'div',
                    Class: 'form-text',
                    Content: $Context->Helper
                ) : false,
                fn () => !empty($Context->Validation->GetHelpers()) ? new Html(
                    Dom: 'div',
                    Class: 'form-text',
                    Content: $Context->Validation->GetHelpers()
                ) : false,
                fn () => !empty($Context->Validation->Errors) ? new Html(
                    Dom: 'div',
                    Class: 'invalid-feedback',
                    Content: $Context->Validation->Errors
                ) : false,
            ]
        );
    }

    private Closure|false|array $Format;

    public function __construct(
        string $Dom = 'input',
        public ?string $Label = null,
        ?string $Class = null,
        ?string $Id = null,
        ?string $Name = null,
        ?string $Max = null,
        ?string $MaxLength = null,
        public ?string $Placeholder = null,
        public ?string $Helper = null,
        ?InputType $Type = InputType::Text,
        mixed $Value = null,
        Closure|bool|null $Disabled = null,
        Closure|bool|null $ReadOnly = null,
        Closure|false|array|null $Format = null,
        IValidationRule|array|null $ValidationRule = null
    ) {
        parent::__construct(
            Dom: $Dom,
            Id: $Id,
            Name: $Name,
            Class: $Class ?? 'form-control',
            Type: $Type,
            Max: $Max,
            MaxLength: $MaxLength,
            Value: $Value,
            Disabled: $Disabled,
            ReadOnly: $ReadOnly
        );
        if (is_array($Format) && !is_callable($Format) && $Format !== false) {
            throw new Exception('Closure must be a callable');
        }
        $this->Format = $Format !== null ? ($Format !== false ? $Format() : false) : static::Default();

        $this->Validation = new Validation($this->Value instanceof Closure ? $this->Value : fn () => $this->Value);

        if ($ValidationRule !== null) {
            if (is_array($ValidationRule)) {
                $this->Validation->AddRule(...$ValidationRule);
            } else {
                $this->Validation->AddRule($ValidationRule);
            }
        }
    }

    public function __toString()
    {
        if ($this->Format === false) {
            return parent::__toString();
        }

        $Format = $this->Format->__invoke($this);

        return $Format;
    }
}
