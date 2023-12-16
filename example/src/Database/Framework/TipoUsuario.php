<?php

namespace Database\Framework;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Database\Enumerations\DbType;

class TipoUsuario extends DbTable
{
    public function __construct(
        #[Field(
            Field: 'id_tipousuario',
            Type: DbType::UnsignedInt,
            PrimaryKey: true,
            AutoIncrement: true
        )]
        public Field|int|null $id_tipousuario = null,
        #[Field(
            Field: 'tus_nombre',
            Type: DbType::Varchar,
            FieldLength: 50,
            IsUnique: true,
            MinLength: 3,
            MaxLength: 50,
            Label: 'Nombre'
        )]
        public Field|string|null $tus_nombre = null,
        #[Field(
            Field: 'tus_estado',
            Type: DbType::UnsignedInt,
            Default: 1,
            Filter: 1
        )]
        public Field|int|null $tus_estado = null
    ) {
    }
}
