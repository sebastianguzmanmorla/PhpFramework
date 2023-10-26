<?php

namespace PhpFramework\Html\Components;

use PhpFramework\Html\Html;
use PhpFramework\Html\HtmlAttribute;

class Script extends Html
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
