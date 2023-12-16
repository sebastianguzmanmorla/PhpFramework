<?php

namespace PhpFramework\Html\Components;

use PhpFramework\Html\Markup;

class Script extends Markup
{
    public function __construct(
        string $Src,
        #[HtmlAttribute('integrity')]
        public ?string $Integrity = null,
        #[HtmlAttribute('crossorigin')]
        public string $CrossOrigin = 'anonymous',
        #[HtmlAttribute('referrerpolicy')]
        public string $ReferrerPolicy = 'no-referrer'
    ) {
        parent::__construct(
            Dom: 'script',
            Src: $Src
        );
    }
}
