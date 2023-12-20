<?php

namespace Database\Framework;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Database\Enumerations\DbType;

class Permiso extends DbTable
{
    public function __construct(
        #[Field(
            Field: 'id_permiso',
            Type: DbType::UnsignedInt,
            PrimaryKey: true,
            AutoIncrement: true
        )]
        public Field|int|null $id_permiso = null,
        #[Field(
            Field: 'id_permisopadre',
            Type: DbType::UnsignedInt,
            Default: 0
        )]
        public Field|int|null $id_permisopadre = null,
        #[Field(
            Field: 'id_modulo',
            Type: DbType::UnsignedInt
        )]
        public Field|int|null $id_modulo = null,
        #[Field(
            Field: 'per_icon',
            Type: DbType::Varchar,
            FieldLength: 50
        )]
        public Field|string|null $per_icon = null,
        #[Field(
            Field: 'per_nombre',
            Type: DbType::Varchar,
            FieldLength: 50
        )]
        public Field|string|null $per_nombre = null,
        #[Field(
            Field: 'per_route',
            Type: DbType::Varchar,
            FieldLength: 255
        )]
        public Field|string|null $per_route = null,
        #[Field(
            Field: 'per_orden',
            Type: DbType::UnsignedInt,
            Default: 0
        )]
        public Field|int|null $per_orden = null,
        #[Field(
            Field: 'per_requerido',
            Type: DbType::UnsignedInt,
            Default: 0
        )]
        public Field|int|null $per_requerido = null,
        #[Field(
            Field: 'per_estado',
            Type: DbType::UnsignedInt,
            Default: 1,
            Filter: 1
        )]
        public Field|int|null $per_estado = null
    ) {
    }
}
