<?php

namespace PhpFramework\Html;

use PhpFramework\Html\Enums\ButtonType;
use PhpFramework\Html\Enums\Color;

class FormButton extends Html
{
    public function __construct(
        ?string $Id = null,
        ?string $Name = null,
        ButtonType $Type = ButtonType::Submit,
        ?string $Class = null,
        ?Color $Color = null,
        ?string $Style = null,
        ?string $Icon = null,
        ?string $Title = null,
        ?string $Label = null,
        ?string $OnClick = null
    ) {
        parent::__construct(
            Dom: 'button',
            Id: $Id,
            Name: $Name,
            Type: $Type,
            Class: $Class ?? ($Color !== null ? 'btn btn-' . $Color->value : null),
            Style: $Style,
            Icon: $Icon,
            Title: $Title,
            Content: $Label !== null ? new Html(Dom: 'Span', Content: $Label) : null,
            OnClick: $OnClick
        );
    }
}
