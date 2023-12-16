<?php

namespace Model\TipoUsuario;

use PhpFramework\Database\DbTable;
use PhpFramework\Html\Markup;

class TipoUsuarioItem extends DbTable
{
    public function __construct(
        public ?int $id_tipousuario = null,
        public ?string $tus_nombre = null,
        public ?Markup $Acciones = null
    ) {
    }
}
