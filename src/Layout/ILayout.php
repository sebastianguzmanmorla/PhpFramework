<?php

namespace PhpFramework\Layout;

use PhpFramework\Response\ViewResponse;

interface ILayout
{
    public function Render(ViewResponse $ViewResponse): void;
}
