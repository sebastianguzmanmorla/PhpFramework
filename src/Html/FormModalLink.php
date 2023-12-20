<?php

namespace PhpFramework\Html;

use Closure;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Url;

class FormModalLink extends FormLink
{
    public function __construct(
        string $Modal,
        public Url|Closure|string|null $Action = null,
        ?string $Id = null,
        ?string $Class = null,
        ?Color $Color = null,
        ?string $Style = null,
        ?string $Icon = null,
        ?string $Title = null,
        ?string $Label = null,
        array $Values = []
    ) {
        if ($this->Action instanceof Closure) {
            $this->Action = new Url($this->Action);
        }
        if ($this->Action instanceof Url) {
            $this->Action = $this->Action->Url;
        }

        $Values['action'] = $this->Action;

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
