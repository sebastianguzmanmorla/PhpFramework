<?php

namespace PhpFramework\Response\Html;

use PhpFramework\Layout\ILayout;

class Response
{
    public ?string $Title = null;

    public ?string $Icon = null;

    public ?ILayout $Layout = null;

    private static ?self $Instance = null;

    final public static function Instance(): static
    {
        return static::$Instance ??= new static();
    }

    public function Render(ViewResponse $ViewResponse): void
    {
        $this->Layout->Render($ViewResponse, $this);
    }
}
