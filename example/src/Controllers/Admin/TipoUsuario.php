<?php

namespace Controllers\Admin;

use Database\Framework\TipoUsuario as DbTipoUsuario;
use Model\TipoUsuario\TipoUsuarioItem;
use PhpFramework\Attributes\Hashid;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Database\Enumerations\DbWhere;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\FormLink;
use PhpFramework\Html\Markup;
use PhpFramework\Request\Method;
use PhpFramework\Request\TableRequest;
use PhpFramework\Response\ErrorHtmlResponse;
use PhpFramework\Response\IResponse;
use PhpFramework\Response\RedirectResponse;
use PhpFramework\Response\StatusCode;
use PhpFramework\Response\TableResponse;
use PhpFramework\Route;
use Request\PermisoUsuarioFilter;

class TipoUsuario extends Controller
{
    #[Singleton]
    private \Database\Framework $Database;

    #[Route('Admin/TipoUsuario'), PermisoUsuarioFilter]
    public function Index(
        ?int $id_tipousuario = null,
        ?string $tus_nombre = null
    ): \Views\Admin\TipoUsuario\Index {
        $View = new \Views\Admin\TipoUsuario\Index();

        if ($id_tipousuario !== null) {
            $View->id_tipousuario->Value = $id_tipousuario;

            $View->FiltersOpen = true;
        }

        if ($tus_nombre !== null) {
            $View->tus_nombre->Value = $tus_nombre;

            $View->FiltersOpen = true;
        }

        return $View;
    }

    #[Route('Admin/TipoUsuario/Listado', Method::POST), PermisoUsuarioFilter]
    public function Listado(
        ?int $id_tipousuario = null,
        ?string $tus_nombre = null,
        ?TableRequest $TableRequest = null
    ): TableResponse {
        $View = new \Views\Admin\TipoUsuario\Index();

        $TipoUsuario_set = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->tus_estado == 1);

        if ($id_tipousuario !== null) {
            $TipoUsuario_set = $TipoUsuario_set->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $id_tipousuario);
        }

        if ($tus_nombre !== null) {
            $TipoUsuario_set = $TipoUsuario_set->Where(fn (DbTipoUsuario $x) => DbWhere::Like($x->tus_nombre, $tus_nombre));
        }

        $TipoUsuarios = $TipoUsuario_set->Select(
            TableRequest: $TableRequest,
            Select: fn (DbTipoUsuario $TipoUsuario): TipoUsuarioItem => new TipoUsuarioItem(
                id_tipousuario: $TipoUsuario->id_tipousuario,
                tus_nombre: $TipoUsuario->tus_nombre,
                Acciones: new Markup(
                    Class: 'btn-group btn-group-sm',
                    Role: 'group',
                    AriaLabel: '...',
                    Content: [
                        new FormLink(Href: fn (TipoUsuario $x) => $x->Editar(id_tipousuario: $TipoUsuario->id_tipousuario), Color: Color::Light, Icon: 'fa fa-edit fa-lg', Title: 'Editar Tipo de Usuario'),
                        $View->Borrar->ModalLink(Action: fn (TipoUsuario $x) => $x->Borrar(id_tipousuario: $TipoUsuario->id_tipousuario), Color: Color::Danger, Icon: 'fa fa-trash fa-lg', Title: 'Borrar Tipo de Usuario'),
                    ]
                )
            )
        );

        return new TableResponse($TipoUsuarios, $TableRequest);
    }

    #[Route('Admin/TipoUsuario/Editar'), PermisoUsuarioFilter]
    public function Editar(
        #[Hashid(Method::GET)]
        int $id_tipousuario
    ): IResponse {
        $View = new \Views\Admin\TipoUsuario\Editar();

        $TipoUsuario_rs = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $id_tipousuario && $x->tus_estado == 1)
            ->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorHtmlResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        $View->TipoUsuario = $TipoUsuario_rs->current();

        return $View;
    }

    #[Route('Admin/TipoUsuario/Editar', Method: Method::POST), PermisoUsuarioFilter]
    public function EditarPost(
        #[Hashid(Method::GET)]
        int $id_tipousuario,
        ?string $tus_nombre = null
    ): IResponse {
        $TipoUsuario_set = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $id_tipousuario && $x->tus_estado == 1);

        $TipoUsuario_rs = $TipoUsuario_set->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorHtmlResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        $View = new \Views\Admin\TipoUsuario\Editar();

        $View->TipoUsuario = $TipoUsuario_rs->current();
        $View->TipoUsuario->tus_nombre = trim($tus_nombre ?? '');

        if (!$View->Validate($View->TipoUsuario)) {
            return $View;
        }
        $TipoUsuario_set->Update($View->TipoUsuario);

        return new RedirectResponse(fn (TipoUsuario $x) => $x->Index(id_tipousuario: $View->TipoUsuario->id_tipousuario));
    }

    #[Route('Admin/TipoUsuario/Crear'), PermisoUsuarioFilter]
    public function Crear(): \Views\Admin\TipoUsuario\Crear
    {
        $View = new \Views\Admin\TipoUsuario\Crear();

        $View->TipoUsuario = new DbTipoUsuario();

        return $View;
    }

    #[Route('Admin/TipoUsuario/Crear', Method: Method::POST), PermisoUsuarioFilter]
    public function CrearPost(
        ?string $tus_nombre = null
    ): IResponse {
        $View = new \Views\Admin\TipoUsuario\Crear();

        $View->TipoUsuario = new DbTipoUsuario();
        $View->TipoUsuario->tus_nombre = trim($tus_nombre ?? '');

        if (!$View->Validate()) {
            return $View;
        }
        $this->Database->TipoUsuario->Insert($View->TipoUsuario);

        return new RedirectResponse(fn (TipoUsuario $x) => $x->Editar(id_tipousuario: $View->TipoUsuario->id_tipousuario));
    }

    #[Route('Admin/TipoUsuario/Borrar', Method: Method::POST), PermisoUsuarioFilter]
    public function Borrar(
        #[Hashid(Method::GET)]
        int $id_tipousuario
    ): IResponse {
        $TipoUsuario_set = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $id_tipousuario && $x->tus_estado == 1);

        $TipoUsuario_rs = $TipoUsuario_set->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorHtmlResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        $TipoUsuario = new DbTipoUsuario();
        $TipoUsuario->tus_estado = 0;

        $TipoUsuario_set->Update($TipoUsuario);

        return new RedirectResponse(fn (TipoUsuario $x) => $x->Index());
    }
}
