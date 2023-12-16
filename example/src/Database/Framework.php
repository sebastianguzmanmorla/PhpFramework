<?php

namespace Database;

use Database\Framework\Modulo;
use Database\Framework\Permiso;
use Database\Framework\PermisoTipoUsuario;
use Database\Framework\PermisoUsuario;
use Database\Framework\TipoUsuario;
use Database\Framework\Usuario;
use PhpFramework\Database\Attributes\Schema;
use PhpFramework\Database\Attributes\Table;
use PhpFramework\Database\DbSchema;
use PhpFramework\Database\DbSet;
use PhpFramework\Database\DbTable;

#[Schema(Name: 'phpframework')]
class Framework extends DbSchema
{
    #[Table(Class: Modulo::class, Name: 'modulo')]
    public readonly DbSet|DbTable $Modulo;

    #[Table(Class: Permiso::class, Name: 'permiso')]
    public readonly DbSet|DbTable $Permiso;

    #[Table(Class: PermisoTipoUsuario::class, Name: 'permisotipousuario')]
    public readonly DbSet|DbTable $PermisoTipoUsuario;

    #[Table(Class: PermisoUsuario::class, Name: 'permisousuario')]
    public readonly DbSet|DbTable $PermisoUsuario;

    #[Table(Class: TipoUsuario::class, Name: 'tipousuario')]
    public readonly DbSet|DbTable $TipoUsuario;

    #[Table(Class: Usuario::class, Name: 'usuario')]
    public readonly DbSet|DbTable $Usuario;
}
