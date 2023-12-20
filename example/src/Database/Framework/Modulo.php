<?php

namespace Database\Framework;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Database\Enumerations\DbType;

class Modulo extends DbTable
{
    public function __construct(
        #[Field(
            Field: 'id_modulo',
            Type: DbType::UnsignedInt,
            PrimaryKey: true,
            AutoIncrement: true
        )]
        public Field|int|null $id_modulo = null,
        #[Field(
            Field: 'id_modulopadre',
            Type: DbType::UnsignedInt,
            Default: 0
        )]
        public Field|int|null $id_modulopadre = null,
        #[Field(
            Field: 'mod_icon',
            Type: DbType::Varchar,
            FieldLength: 50,
            AllowNull: true
        )]
        public Field|string|null $mod_icon = null,
        #[Field(
            Field: 'mod_nombre',
            Type: DbType::Varchar,
            FieldLength: 50
        )]
        public Field|string|null $mod_nombre = null,
        #[Field(
            Field: 'mod_orden',
            Type: DbType::UnsignedInt,
            Default: 0
        )]
        public Field|int|null $mod_orden = null,
        #[Field(
            Field: 'mod_estado',
            Type: DbType::UnsignedInt,
            Default: 1,
            Filter: 1
        )]
        public Field|int|null $mod_estado = null
    ) {
    }
}
