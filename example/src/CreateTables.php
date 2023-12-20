<?php

use Database\Framework;
use Database\Framework\Modulo;
use Database\Framework\Permiso;
use Database\Framework\PermisoTipoUsuario;
use Database\Framework\PermisoUsuario;
use Database\Framework\TipoUsuario;
use Database\Framework\Usuario;
use Environment\Config;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Database\Helpers\SqlFormatter;

require_once __DIR__ . '/../../vendor/autoload.php';

spl_autoload_extensions('.php');
spl_autoload_register();

Config::Initialize();

$Database = Singleton::Get(Framework::class);

foreach ($Database->Schema->Tables() as $Table) {
    $Query = $Table->CreateSyntax();
    echo SqlFormatter::format($Query, true);
    $Database->Execute($Query);
}

$Modulos = [
    new Modulo(
        id_modulo: 1,
        mod_nombre: 'Administración',
        mod_icon: 'fa fa-cogs',
        mod_orden: 1
    ),
    new Modulo(
        id_modulo: 2,
        id_modulopadre: 1,
        mod_nombre: 'Tipos de Usuario',
        mod_icon: 'fa fa-tags',
        mod_orden: 1
    ),
    new Modulo(
        id_modulo: 3,
        id_modulopadre: 1,
        mod_nombre: 'Usuarios',
        mod_icon: 'fa fa-user',
        mod_orden: 2
    ),
];

$Query = $Database->Modulo->InsertQuery(...$Modulos);
echo SqlFormatter::format($Query, true);
$Database->Execute($Query);

$Permisos = [
    new Permiso(
        id_permiso: 1,
        id_modulo: 0,
        per_icon: 'fa fa-home',
        per_nombre: 'Dashboard',
        per_route: 'Admin/Index',
        per_orden: 1,
        per_requerido: 0
    ),
    new Permiso(
        id_permiso: 2,
        id_modulo: 2,
        per_icon: 'fa fa-cogs',
        per_nombre: 'Listado Tipos de Usuario',
        per_route: 'Admin/TipoUsuario',
        per_orden: 2,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 3,
        id_modulo: 2,
        id_permisopadre: 2,
        per_icon: 'fa fa-file',
        per_nombre: 'Listado Tipos de Usuario JSON',
        per_route: 'Admin/TipoUsuario/Listado',
        per_orden: 0,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 4,
        id_modulo: 2,
        per_icon: 'fa fa-plus',
        per_nombre: 'Crear Tipo de Usuario',
        per_route: 'Admin/TipoUsuario/Crear',
        per_orden: 1,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 5,
        id_modulo: 2,
        id_permisopadre: 2,
        per_icon: 'fa fa-pencil',
        per_nombre: 'Editar Tipo de Usuario',
        per_route: 'Admin/TipoUsuario/Editar',
        per_orden: 0,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 6,
        id_modulo: 2,
        id_permisopadre: 2,
        per_icon: 'fa fa-trash',
        per_nombre: 'Borrar Tipo de Usuario',
        per_route: 'Admin/TipoUsuario/Borrar',
        per_orden: 0,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 7,
        id_modulo: 3,
        per_icon: 'fa fa-cogs',
        per_nombre: 'Listado Usuarios',
        per_route: 'Admin/Usuario',
        per_orden: 2,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 8,
        id_modulo: 3,
        id_permisopadre: 7,
        per_icon: 'fa fa-file',
        per_nombre: 'Listado Usuarios JSON',
        per_route: 'Admin/Usuario/Listado',
        per_orden: 0,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 9,
        id_modulo: 3,
        per_icon: 'fa fa-plus',
        per_nombre: 'Crear Usuario',
        per_route: 'Admin/Usuario/Crear',
        per_orden: 1,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 10,
        id_modulo: 3,
        id_permisopadre: 7,
        per_icon: 'fa fa-pencil',
        per_nombre: 'Editar Usuario',
        per_route: 'Admin/Usuario/Editar',
        per_orden: 0,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 11,
        id_modulo: 3,
        id_permisopadre: 7,
        per_icon: 'fa fa-trash',
        per_nombre: 'Borrar Usuario',
        per_route: 'Admin/Usuario/Borrar',
        per_orden: 0,
        per_requerido: 1
    ),
    new Permiso(
        id_permiso: 12,
        id_modulo: 3,
        id_permisopadre: 7,
        per_icon: 'fa fa-key',
        per_nombre: 'Modificar Contraseña',
        per_route: 'Admin/Usuario/ModificarPassword',
        per_orden: 0,
        per_requerido: 1
    ),
];

$Query = $Database->Permiso->InsertQuery(...$Permisos);
echo SqlFormatter::format($Query, true);
$Database->Execute($Query);

$TiposUsuario = [
    new TipoUsuario(
        id_tipousuario: 1,
        tus_nombre: 'Administrador'
    ),
    new TipoUsuario(
        id_tipousuario: 2,
        tus_nombre: 'Usuario'
    ),
];

$Query = $Database->TipoUsuario->InsertQuery(...$TiposUsuario);
echo SqlFormatter::format($Query, true);
$Database->Execute($Query);

$Usuarios = [
    new Usuario(
        id_usuario: 1,
        id_tipousuario: 1,
        usu_rut: '1-9',
        usu_login: 'admin',
        usu_pass: password_hash('NCaJjtyU9KfrpcL6kRsDWn', PASSWORD_BCRYPT),
        usu_mail: 'admin@asdf.asdf',
        usu_nombre: 'Administrador',
        usu_apellido: 'Ejemplo'
    ),
];

$Query = $Database->Usuario->InsertQuery(...$Usuarios);
echo SqlFormatter::format($Query, true);
$Database->Execute($Query);

$PermisoTipoUsuario = [];

foreach ($Permisos as $Permiso) {
    if ($Permiso->per_requerido == 1) {
        $PermisoTipoUsuario[] = new PermisoTipoUsuario(
            id_tipousuario: 1,
            id_permiso: $Permiso->id_permiso
        );
    }
}

$Query = $Database->PermisoTipoUsuario->InsertQuery(...$PermisoTipoUsuario);
echo SqlFormatter::format($Query, true);
$Database->Execute($Query);

$PermisoUsuario = [];

foreach ($Permisos as $Permiso) {
    if ($Permiso->per_requerido == 1) {
        $PermisoUsuario[] = new PermisoUsuario(
            id_usuario: 1,
            id_permiso: $Permiso->id_permiso
        );
    }
}

$Query = $Database->PermisoUsuario->InsertQuery(...$PermisoUsuario);
echo SqlFormatter::format($Query, true);
$Database->Execute($Query);
