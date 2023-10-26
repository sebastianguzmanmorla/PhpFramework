<?php

namespace PhpFramework\Html\Components;

use PhpFramework\Html\Enums\AlertType;
use PhpFramework\Html\Html;

class Alert extends Html
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
