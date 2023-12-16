<?php

namespace Controllers\Admin;

use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Request\Method;
use PhpFramework\Response\HtmlResponse;
use PhpFramework\Route;
use Request\PermisoUsuarioFilter;

class Index extends Controller
{
    #[Singleton]
    public \Database\Framework $Database;

    #[Route('Admin/Index'), PermisoUsuarioFilter]
    public function Index(): HtmlResponse
    {
        $View = new \Views\Admin\Index();

        return $View;
    }

    #[Route('Admin/Index', Method::POST), PermisoUsuarioFilter]
    public function IndexPost(): HtmlResponse
    {
        $View = new \Views\Admin\Index();

        return $View;
    }
}
