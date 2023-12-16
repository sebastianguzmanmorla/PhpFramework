<?php

namespace PhpFramework\Html\Components;

use PhpFramework\Html\Markup;

class Stylesheet extends Markup
{
    public function __construct(
        string $Href,
        #[HtmlAttribute('integrity')]
        public ?string $Integrity = null,
        #[HtmlAttribute('crossorigin')]
        public string $CrossOrigin = 'anonymous',
        #[HtmlAttribute('referrerpolicy')]
        public string $ReferrerPolicy = 'no-referrer'
    ) {
        parent::__construct(
            Dom: 'link',
            Href: $Href,
            Rel: 'stylesheet'
        );
    }
}
