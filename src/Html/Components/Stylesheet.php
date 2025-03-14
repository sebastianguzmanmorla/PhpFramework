<?php

namespace PhpFramework\Html\Components;

use PhpFramework\Html\Markup;
use PhpFramework\Html\MarkupAttribute;

class Stylesheet extends Markup
{
    public function __construct(
        string $Href,
        #[MarkupAttribute('integrity')]
        public ?string $Integrity = null,
        #[MarkupAttribute('crossorigin')]
        public string $CrossOrigin = 'anonymous',
        #[MarkupAttribute('referrerpolicy')]
        public string $ReferrerPolicy = 'no-referrer'
    ) {
        parent::__construct(
            Dom: 'link',
            Href: $Href,
            Rel: 'stylesheet'
        );
    }
}
