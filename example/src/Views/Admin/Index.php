<?php

namespace Views\Admin;

use Model\Layout\HtmlResponse;

class Index extends HtmlResponse
{
    public function Init(): void
    {
        $this->Title = 'Dashboard';
    }

    public function Body(): void
    {
        ?>
        <div class="card shadow">
            <div class="card-body">
                <p class="card-text">Bienvenido al sistema.</p>
            </div>
        </div>
<?php
    }
}
