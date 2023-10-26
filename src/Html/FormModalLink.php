<?php

namespace PhpFramework\Html;

use PhpFramework\Html\Enums\Color;

class FormModalLink extends FormLink
{
    public function __construct(
        string $Modal,
        ?string $Id = null,
        ?string $Class = null,
        ?Color $Color = null,
        ?string $Style = null,
        ?string $Icon = null,
        ?string $Title = null,
        ?string $Label = null,
        array $Values = []
    ) {
        parent::__construct(
            Href: '#',
            Id: $Id,
            Class: $Class,
            Color: $Color,
            Style: $Style,
            Icon: $Icon,
            Title: $Title,
            Label: $Label,
            OnClick: fn () => "$('#" . $Modal . "').loadModal(" . (count($Values) > 0 ? json_encode($Values) : '') . ');'
        );
    }
}
