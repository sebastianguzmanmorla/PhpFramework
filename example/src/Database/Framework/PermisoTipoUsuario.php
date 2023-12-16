<?php

namespace Database\Framework;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Database\Enumerations\DbType;

class PermisoTipoUsuario extends DbTable
{
    public function __construct(
        #[Field(
            Field: 'id_permiso',
            Type: DbType::UnsignedInt,
            PrimaryKey: true
        )]
        public Field|int|null $id_permiso = null,
        #[Field(
            Field: 'id_tipousuario',
            Type: DbType::UnsignedInt,
            PrimaryKey: true
        )]
        public Field|int|null $id_tipousuario = null
    ) {
    }
}
