<?php

namespace Controllers\Admin;

use Database\Framework\TipoUsuario;
use Database\Framework\Usuario as DbUsuario;
use Model\Usuario\UsuarioItem;
use PhpFramework\Attributes\Hashid;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Database\Enumerations\DbWhere;
use PhpFramework\Html\Components\Alert;
use PhpFramework\Html\Enums\AlertType;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\FormLink;
use PhpFramework\Html\FormSelectOption;
use PhpFramework\Html\Markup;
use PhpFramework\Request\Enum\Method;
use PhpFramework\Request\TableRequest;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Html\ErrorResponse;
use PhpFramework\Response\Interface\IResponse;
use PhpFramework\Response\Json\TableResponse;
use PhpFramework\Response\RedirectResponse;
use PhpFramework\Route;
use Request\PermisoUsuarioFilter;

class Usuario extends Controller
{
    #[Singleton]
    private \Database\Framework $Database;

    #[Route('Admin/Usuario'), PermisoUsuarioFilter]
    public function Index(
        ?int $id_usuario = null,
        ?int $id_tipousuario = null,
        ?string $usu_mail = null,
        ?string $usu_rut = null,
        ?string $usu_nombre = null
    ): \Views\Admin\Usuario\Index {
        $View = new \Views\Admin\Usuario\Index();

        $TipoUsuario_rs = $this->Database->TipoUsuario
            ->Where(fn (TipoUsuario $x) => $x->tus_estado == 1)
            ->Select(fn (TipoUsuario $x): FormSelectOption => new FormSelectOption(
                Text: $x->tus_nombre,
                Value: $x->id_tipousuario
            ));

        $View->id_tipousuario->Options[] = new FormSelectOption(Text: '= TODOS =', Value: 0);

        foreach ($TipoUsuario_rs as $TipoUsuario) {
            $View->id_tipousuario->Options[] = $TipoUsuario;
        }

        if ($id_usuario !== null) {
            $View->id_usuario->Value = $id_usuario;

            $View->FiltersOpen = true;
        }

        if ($id_tipousuario > 0) {
            $View->id_tipousuario->Value = $id_tipousuario;

            $View->FiltersOpen = true;
        }

        if ($usu_mail !== null) {
            $View->usu_mail->Value = $usu_mail;

            $View->FiltersOpen = true;
        }

        if ($usu_rut !== null) {
            $View->usu_rut->Value = $usu_rut;

            $View->FiltersOpen = true;
        }

        if ($usu_nombre !== null) {
            $View->usu_nombre->Value = $usu_nombre;

            $View->FiltersOpen = true;
        }

        return $View;
    }

    #[Route('Admin/Usuario/Listado', Method::POST), PermisoUsuarioFilter]
    public function Listado(
        ?int $id_usuario = null,
        ?int $id_tipousuario = null,
        ?string $usu_mail = null,
        ?string $usu_rut = null,
        ?string $usu_nombre = null,
        ?TableRequest $TableRequest = null
    ): TableResponse {
        $View = new \Views\Admin\Usuario\Index();

        $usuario_set = $this->Database->Usuario
            ->InnerJoin(fn (TipoUsuario $x, DbUsuario $y) => $x->id_tipousuario == $y->id_tipousuario)
            ->Where(fn (DbUsuario $x) => $x->usu_estado == 1);

        if ($id_usuario !== null) {
            $usuario_set = $usuario_set->Where(fn (DbUsuario $x) => $x->id_usuario == $id_usuario);
        }

        if ($id_tipousuario > 0) {
            $usuario_set = $usuario_set->Where(fn (DbUsuario $x) => $x->id_tipousuario == $id_tipousuario);
        }

        if ($usu_mail !== null) {
            $usuario_set = $usuario_set->Where(fn (DbUsuario $x) => DbWhere::Like($x->usu_mail, $usu_mail));
        }

        if ($usu_rut !== null) {
            $usuario_set = $usuario_set->Where(fn (DbUsuario $x) => DbWhere::Like($x->usu_rut, $usu_rut));
        }

        if ($usu_nombre !== null) {
            $usuario_set = $usuario_set->Where(fn (DbUsuario $x) => DbWhere::Like($x->usu_nombre, $usu_nombre) || DbWhere::Like($x->usu_apellido, $usu_nombre));
        }

        $Usuarios = $usuario_set->Select(
            TableRequest: $TableRequest,
            Select: fn (DbUsuario $Usuario, TipoUsuario $TipoUsuario): UsuarioItem => new UsuarioItem(
                id_usuario: $Usuario->id_usuario,
                tus_nombre: $TipoUsuario->tus_nombre,
                usu_mail: $Usuario->usu_mail,
                usu_rut: $Usuario->usu_rut,
                usu_nombre: $Usuario->usu_nombre . ' ' . $Usuario->usu_apellido,
                usu_ultimologin: $Usuario->usu_ultimologin?->format('d-m-Y H:i:s'),
                Acciones: new Markup(
                    Class: 'btn-group btn-group-sm',
                    Role: 'group',
                    AriaLabel: '...',
                    Content: [
                        new FormLink(Href: fn (Usuario $x) => $x->Editar(id_usuario: $Usuario->id_usuario), Color: Color::Light, Icon: 'fa fa-edit fa-lg', Title: 'Editar Usuario'),
                        new FormLink(Href: fn (Usuario $x) => $x->Editar(id_usuario: $Usuario->id_usuario), Color: Color::Light, Icon: 'fa fa-ticket-alt fa-lg', Title: 'Editar Permisos'),
                        new FormLink(Href: fn (Usuario $x) => $x->ModificarPassword(id_usuario: $Usuario->id_usuario), Color: Color::Light, Icon: 'fa fa-key fa-lg', Title: 'Modificar Contraseña'),
                        $View->Borrar->ModalLink(Action: fn (Usuario $x) => $x->Borrar(id_usuario: $Usuario->id_usuario), Color: Color::Danger, Icon: 'fa fa-trash fa-lg', Title: 'Borrar Usuario'),
                    ]
                )
            )
        );

        return new TableResponse($Usuarios, $TableRequest);
    }

    #[Route('Admin/Usuario/Editar'), PermisoUsuarioFilter]
    public function Editar(
        #[Hashid(Method: Method::GET)]
        int $id_usuario,
    ): \Views\Admin\Usuario\Editar {
        $View = new \Views\Admin\Usuario\Editar();

        $Usuario_rs = $this->Database->Usuario
            ->Where(fn (DbUsuario $x) => $x->id_usuario == $id_usuario && $x->usu_estado == 1)
            ->Select();

        if ($Usuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::NotFound, 'Usuario no encontrado');
        }

        $View->Usuario = $Usuario_rs->current();

        $TipoUsuario_rs = $this->Database->TipoUsuario
            ->Where(fn (TipoUsuario $x) => $x->tus_estado == 1)
            ->Select(fn (TipoUsuario $x): FormSelectOption => new FormSelectOption(
                Text: $x->tus_nombre,
                Value: $x->id_tipousuario
            ));

        $View->id_tipousuario->Options[] = new FormSelectOption(Text: '= SELECCIONE TIPO DE USUARIO =', Value: 0);

        foreach ($TipoUsuario_rs as $TipoUsuario) {
            $View->id_tipousuario->Options[] = $TipoUsuario;
        }

        return $View;
    }

    #[Route('Admin/Usuario/Editar', Method: Method::POST), PermisoUsuarioFilter]
    public function EditarPost(
        #[Hashid(Method: Method::GET)]
        int $id_usuario,
        ?int $id_tipousuario = null,
        ?string $usu_rut = null,
        ?string $usu_mail = null,
        ?string $usu_nombre = null,
        ?string $usu_apellido = null,
    ): IResponse {
        $View = new \Views\Admin\Usuario\Editar();

        $Usuario_set = $this->Database->Usuario
            ->Where(fn (DbUsuario $x) => $x->id_usuario == $id_usuario && $x->usu_estado == 1);

        $Usuario_rs = $Usuario_set->Select();

        if ($Usuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::NotFound, 'Usuario no encontrado');
        }

        $View->Usuario = $Usuario_rs->current();
        $View->Usuario->id_tipousuario = $id_tipousuario;
        $View->Usuario->usu_rut = $usu_rut;
        $View->Usuario->usu_mail = $usu_mail;
        $View->Usuario->usu_nombre = $usu_nombre;
        $View->Usuario->usu_apellido = $usu_apellido;

        $TipoUsuario_rs = $this->Database->TipoUsuario
            ->Where(fn (TipoUsuario $x) => $x->tus_estado == 1)
            ->Select(fn (TipoUsuario $x): FormSelectOption => new FormSelectOption(
                Text: $x->tus_nombre,
                Value: $x->id_tipousuario
            ));

        $View->id_tipousuario->Options[] = new FormSelectOption(Text: '= SELECCIONE TIPO DE USUARIO =', Value: 0);

        foreach ($TipoUsuario_rs as $TipoUsuario) {
            $View->id_tipousuario->Options[] = $TipoUsuario;
        }

        if (!$View->Validate()) {
            return $View;
        }
        $Usuario_set->Update($View->Usuario);

        return new RedirectResponse(fn (Usuario $x) => $x->Index(id_usuario: $View->Usuario->id_usuario));
    }

    #[Route('Admin/Usuario/Crear'), PermisoUsuarioFilter]
    public function Crear(): \Views\Admin\Usuario\Crear
    {
        $View = new \Views\Admin\Usuario\Crear();

        $View->Usuario = new DbUsuario();

        $TipoUsuario_rs = $this->Database->TipoUsuario
            ->Where(fn (TipoUsuario $x) => $x->tus_estado == 1)
            ->Select(fn (TipoUsuario $x): FormSelectOption => new FormSelectOption(
                Text: $x->tus_nombre,
                Value: $x->id_tipousuario
            ));

        $View->id_tipousuario->Options[] = new FormSelectOption(Text: '= SELECCIONE TIPO DE USUARIO =', Value: 0);

        foreach ($TipoUsuario_rs as $TipoUsuario) {
            $View->id_tipousuario->Options[] = $TipoUsuario;
        }

        return $View;
    }

    #[Route('Admin/Usuario/Crear', Method: Method::POST), PermisoUsuarioFilter]
    public function CrearPost(
        ?int $id_tipousuario = null,
        ?string $usu_rut = null,
        ?string $usu_mail = null,
        ?string $usu_pass = null,
        ?string $usu_nombre = null,
        ?string $usu_apellido = null,
    ): IResponse {
        $View = new \Views\Admin\Usuario\Crear();

        $View->Usuario = new DbUsuario();

        $TipoUsuario_rs = $this->Database->TipoUsuario
            ->Where(fn (TipoUsuario $x) => $x->tus_estado == 1)
            ->Select(fn (TipoUsuario $x): FormSelectOption => new FormSelectOption(
                Text: $x->tus_nombre,
                Value: $x->id_tipousuario
            ));

        $View->id_tipousuario->Options[] = new FormSelectOption(Text: '= SELECCIONE TIPO DE USUARIO =', Value: 0);

        foreach ($TipoUsuario_rs as $TipoUsuario) {
            $View->id_tipousuario->Options[] = $TipoUsuario;
        }

        $View->Usuario->id_tipousuario = $id_tipousuario;
        $View->Usuario->usu_rut = $usu_rut;
        $View->Usuario->usu_mail = $usu_mail;
        $View->Usuario->usu_pass = $usu_pass;
        $View->Usuario->usu_nombre = $usu_nombre;
        $View->Usuario->usu_apellido = $usu_apellido;

        if (!$View->Validate()) {
            return $View;
        }
        $View->Usuario->usu_pass = password_hash($usu_pass, PASSWORD_BCRYPT);

        $this->Database->Usuario->Insert($View->Usuario);

        return new RedirectResponse(fn (Usuario $x) => $x->Editar(id_usuario: $View->Usuario->id_usuario));
    }

    #[Route('Admin/Usuario/ModificarPassword'), PermisoUsuarioFilter]
    public function ModificarPassword(
        #[Hashid(Method: Method::GET)]
        int $id_usuario,
    ): IResponse {
        $View = new \Views\ModificarPassword();

        $Usuario_rs = $this->Database->Usuario
            ->InnerJoin(fn (TipoUsuario $x, DbUsuario $y) => $x->id_tipousuario == $y->id_tipousuario)
            ->Where(fn (DbUsuario $x) => $x->id_usuario == $id_usuario && $x->usu_estado == 1)
            ->Select();

        if ($Usuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::NotFound, 'Usuario no encontrado');
        }

        $DbItem = $Usuario_rs->current();

        $View->Usuario = $DbItem->Usuario;
        $View->TipoUsuario = $DbItem->TipoUsuario;

        return $View;
    }

    #[Route('Admin/Usuario/ModificarPassword', Method::POST), PermisoUsuarioFilter]
    public function ModificarPasswordPost(
        #[Hashid(Method: Method::GET)]
        int $id_usuario,
        ?string $usu_pass = null
    ): IResponse {
        $View = new \Views\ModificarPassword();

        $Usuario_set = $this->Database->Usuario
            ->InnerJoin(fn (TipoUsuario $x, DbUsuario $y) => $x->id_tipousuario == $y->id_tipousuario)
            ->Where(fn (DbUsuario $x) => $x->id_usuario == $id_usuario && $x->usu_estado == 1);

        $Usuario_rs = $Usuario_set->Select();

        if ($Usuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::NotFound, 'Usuario no encontrado');
        }

        $DbItem = $Usuario_rs->current();

        $View->Usuario = $DbItem->Usuario;
        $View->TipoUsuario = $DbItem->TipoUsuario;

        $View->usu_pass->Value = $usu_pass;

        if ($View->Validate()) {
            $View->Usuario->usu_pass = password_hash($usu_pass, PASSWORD_BCRYPT);

            $Usuario_set->Update($View->Usuario);

            $View->Alerts->AddAlert(new Alert(AlertType::Success, 'Contraseña modificada'));
        }

        $View->usu_pass->Value = null;

        return $View;
    }

    #[Route('Admin/Usuario/Borrar', Method: Method::POST), PermisoUsuarioFilter]
    public function Borrar(
        #[Hashid(Method: Method::GET)]
        int $id_usuario
    ): IResponse {
        $Usuario_set = $this->Database->Usuario
            ->Where(fn (DbUsuario $x) => $x->id_usuario == $id_usuario && $x->usu_estado == 1);

        $Usuario_rs = $Usuario_set->Select();

        if ($Usuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::NotFound, 'Usuario no encontrado');
        }

        $Usuario = new DbUsuario();
        $Usuario->usu_estado = 0;

        $Usuario_set->Update($Usuario);

        return new RedirectResponse(fn (Usuario $x) => $x->Index());
    }
}
