<?php

namespace PhpFramework\Layout;

use PhpFramework\Response\Html\ViewResponse;

interface ILayout
{
    public function Render(ViewResponse $ViewResponse): void;
}
