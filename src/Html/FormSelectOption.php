<?php

namespace PhpFramework\Html;

use PhpFramework\Database\DbTable;

class FormSelectOption extends DbTable
{
    public function __construct(
        public mixed $Text = null,
        public mixed $Value = null,
        public bool $Selected = false
    ) {
    }
}
