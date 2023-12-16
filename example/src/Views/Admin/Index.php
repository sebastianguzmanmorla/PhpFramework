<?php

namespace Views\Admin;

use PhpFramework\Response\HtmlResponse;

class Index extends HtmlResponse
{
    public function Init(): void
    {
        $this->Title = 'Dashboard';
    }

    public function Body(): void
    {
        ?>
<?php
    }
}
