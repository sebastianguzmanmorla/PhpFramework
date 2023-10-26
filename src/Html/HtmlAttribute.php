<?php

namespace PhpFramework\Html;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HtmlAttribute
{
    public function __construct(
        public string $name
    ) {
    }
}
