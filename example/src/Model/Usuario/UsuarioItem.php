<?php

namespace Model\Usuario;

use PhpFramework\Database\DbTable;
use PhpFramework\Html\Markup;

class UsuarioItem extends DbTable
{
    public function __construct(
        public ?int $id_usuario = null,
        public ?string $tus_nombre = null,
        public ?string $usu_login = null,
        public ?string $usu_mail = null,
        public ?string $usu_rut = null,
        public ?string $usu_nombre = null,
        public ?string $usu_ultimologin = null,
        public ?Markup $Acciones = null
    ) {
    }
}
