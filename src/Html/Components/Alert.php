<?php

namespace PhpFramework\Html\Components;

use PhpFramework\Html\Enums\AlertType;
use PhpFramework\Html\Markup;

class Alert extends Markup
{
    public function __construct(
        public AlertType $AlertType,
        public string $AlertMessage
    ) {
        parent::__construct(
            Class: fn () => $this->AlertType->value,
            Role: 'alert',
            Content: fn () => $this->AlertMessage
        );
    }
}
