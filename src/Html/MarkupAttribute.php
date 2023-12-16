<?php

namespace PhpFramework\Html;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MarkupAttribute
{
    public function __construct(
        public string $name
    ) {
    }
}
