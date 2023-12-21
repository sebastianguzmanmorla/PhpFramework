<?php

namespace PhpFramework\Response;

use PhpFramework\Html\Components\Alerts;
use PhpFramework\Html\Components\Scripts;
use PhpFramework\Html\Components\Stylesheets;
use PhpFramework\Html\Markup;
use PhpFramework\Layout\ILayout;

class HtmlResponse
{
    public ?string $Title = null;

    public ?string $Icon = null;

    public Stylesheets $Stylesheets;

    public Scripts $Scripts;

    public Alerts $Alerts;

    public ?string $Project = null;

    public ?string $Author = null;

    public Markup|string|null $Copyright = null;

    public ?ILayout $Layout = null;

    private static ?self $Instance = null;

    final public static function Instance(): static
    {
        return static::$Instance ??= new static();
    }

    final public static function InitializeDefault(
        string $Project = '',
        string $Author = '',
        ?ILayout $Layout = null
    ): void {
        static::$Instance = new static();

        static::$Instance->Project = $Project;
        static::$Instance->Author = $Author;

        static::$Instance->Copyright ??= new Markup(
            Dom: 'div',
            Class: 'text-dark order-2 order-md-1',
            Content: date('Y') . '© ' . static::$Instance->Author . ' / ' . static::$Instance->Project
        );

        static::$Instance->Layout = $Layout;

        static::$Instance->Stylesheets = new Stylesheets();

        static::$Instance->Scripts = new Scripts();

        static::$Instance->Alerts = new Alerts();
    }

    public function Render(ViewResponse $ViewResponse): void
    {
        $this->Layout->Render($ViewResponse, $this);
    }
}
