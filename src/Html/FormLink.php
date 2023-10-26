<?php

namespace PhpFramework\Html;

use Closure;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Url;

class FormLink extends Html
{
    public function __construct(
        Url|Closure|string|null $Href = null,
        ?string $Target = null,
        ?string $Id = null,
        ?string $Class = null,
        ?Color $Color = null,
        ?string $Style = null,
        ?string $Icon = null,
        ?string $Title = null,
        ?string $Label = null,
        Html|array|string|null $Content = null,
        Closure|string|null $OnClick = null,
        ?string $Role = null,
        ?string $DataBsDismiss = null,
        ?string $DataBsToggle = null,
        ?string $DataBsTarget = null,
        ?string $DataBsPlacement = null,
        ?string $AriaLabel = null,
        ?string $AriaCurrent = null,
        ?string $AriaExpanded = null
    ) {
        parent::__construct(
            Dom: 'a',
            Href: $Href,
            Target: $Target,
            Id: $Id,
            Class: $Class ?? ($Color !== null ? 'btn btn-' . $Color->value : null),
            Style: $Style,
            Icon: $Icon,
            Title: $Title,
            Content: $Label !== null ? new Html(Dom: 'span', Content: $Label) : null ?? $Content,
            OnClick: $OnClick,
            Role: $Role,
            DataBsDismiss: $DataBsDismiss,
            DataBsToggle: $DataBsToggle ?? ($Title !== null ? 'tooltip' : null),
            DataBsTarget: $DataBsTarget,
            DataBsPlacement: $DataBsPlacement ?? ($Title !== null ? 'top' : null),
            AriaLabel: $AriaLabel,
            AriaCurrent: $AriaCurrent,
            AriaExpanded: $AriaExpanded
        );
    }
}
