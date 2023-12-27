<?php

namespace Views\Admin;

use PhpFramework\Response\Html\ViewResponse;

class Index extends ViewResponse
{
    public function Initialize(): void
    {
        $this->Title = 'Inicio';
        $this->Icon = 'fa fa-home';
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
