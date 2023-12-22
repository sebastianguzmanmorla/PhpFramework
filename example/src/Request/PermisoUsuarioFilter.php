<?php

namespace Request;

use Attribute;
use Database\Framework\Permiso;
use Database\Framework\PermisoUsuario;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Database\Enumerations\DbWhere;
use PhpFramework\Request\IRequestFilter;
use PhpFramework\Response\ErrorResponse;
use PhpFramework\Response\IResponse;
use PhpFramework\Response\RedirectResponse;
use PhpFramework\Response\StatusCode;
use PhpFramework\Router;

#[Attribute(Attribute::TARGET_METHOD)]
class PermisoUsuarioFilter implements IRequestFilter
{
    public static ?int $id_usuario = null;

    public static ?string $per_route = null;

    public static ?Permiso $Permiso = null;

    #[Singleton]
    private \Database\Framework $Database;

    public function Filter(): ?IResponse
    {
        if (!isset($_SESSION['Usuario']['id_usuario'])) {
            return new RedirectResponse(fn (\Controllers\Index $x) => $x->Index());
        }

        static::$id_usuario = $_SESSION['Usuario']['id_usuario'];

        static::$per_route = $_GET[Router::Route] ?? '';

        $id_usuario = static::$id_usuario;
        $per_route = static::$per_route;

        $permiso_rs = $this->Database->Permiso
            ->LeftJoin(fn (PermisoUsuario $x, Permiso $y) => $x->id_permiso == $y->id_permiso && $x->id_usuario == $id_usuario)
            ->Where(
                fn (Permiso $x, PermisoUsuario $y) => $x->per_route == $per_route && $x->per_estado == 1
                && (DbWhere::IsNotNull($y->id_permiso) || $x->per_requerido == 0)
            )
            ->Select(fn (Permiso $x): Permiso => $x);

        if ($permiso_rs->EOF()) {
            return new ErrorResponse(StatusCode::Unauthorized, 'No tiene permiso');
        }

        static::$Permiso = $permiso_rs->current();

        return null;
    }
}
