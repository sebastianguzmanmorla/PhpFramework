<?php

namespace PhpFramework\Html;

class FormSelectOption
{
    public function __construct(
        public mixed $Text = null,
        public mixed $Value = null,
        public bool $Selected = false
    ) {
    }
}
