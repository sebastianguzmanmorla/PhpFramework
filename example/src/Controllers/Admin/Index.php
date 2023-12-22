<?php

namespace Controllers\Admin;

use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Response\ViewResponse;
use PhpFramework\Route;
use Request\PermisoUsuarioFilter;

class Index extends Controller
{
    #[Singleton]
    private \Database\Framework $Database;

    #[Route('Admin/Index'), PermisoUsuarioFilter]
    public function Index(): ViewResponse
    {
        $View = new \Views\Admin\Index();

        return $View;
    }
}
