<?php

namespace Controllers\Admin;

use PhpFramework\Controller;
use PhpFramework\Response\Html\ViewResponse;
use PhpFramework\Route;
use Request\PermisoUsuarioFilter;

class Index extends Controller
{
    #[Route('Admin/Index'), PermisoUsuarioFilter]
    public function Index(): ViewResponse
    {
        $View = new \Views\Admin\Index();

        return $View;
    }
}
