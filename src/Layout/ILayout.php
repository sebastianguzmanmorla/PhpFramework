<?php

namespace PhpFramework\Layout;

use PhpFramework\Response\HtmlResponse;

interface ILayout
{
    public static function Self(): static;

    public static function Render(HtmlResponse $Context);
}
