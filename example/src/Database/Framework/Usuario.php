<?php

namespace Database\Framework;

use DateTime;
use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbTable;
use PhpFramework\Database\Enumerations\DbType;

class Usuario extends DbTable
{
    public function __construct(
        #[Field(
            Field: 'id_usuario',
            Type: DbType::UnsignedInt,
            PrimaryKey: true,
            AutoIncrement: true
        )]
        public Field|int|null $id_usuario = null,
        #[Field(
            Field: 'id_tipousuario',
            Type: DbType::UnsignedInt
        )]
        public Field|int|null $id_tipousuario = null,
        #[Field(
            Field: 'usu_mail',
            Type: DbType::Varchar,
            FieldLength: 100,
            IsUnique: true,
            IsMail: true
        )]
        public Field|string|null $usu_mail = null,
        #[Field(
            Field: 'usu_pass',
            Type: DbType::Varchar,
            FieldLength: 100,
        )]
        public Field|string|null $usu_pass = null,
        #[Field(
            Field: 'usu_rut',
            Type: DbType::Varchar,
            FieldLength: 10,
            IsUnique: true,
            IsRut: true
        )]
        public Field|string|null $usu_rut = null,
        #[Field(
            Field: 'usu_nombre',
            Type: DbType::Varchar,
            FieldLength: 50,
            Label: 'Nombre',
            MinLength: 3,
            MaxLength: 50
        )]
        public Field|string|null $usu_nombre = null,
        #[Field(
            Field: 'usu_apellido',
            Type: DbType::Varchar,
            FieldLength: 50,
            Label: 'Apellido',
            MinLength: 3,
            MaxLength: 50
        )]
        public Field|string|null $usu_apellido = null,
        #[Field(
            Field: 'usu_ultimologin',
            Type: DbType::DateTime,
            AllowNull: true
        )]
        public Field|DateTime|null $usu_ultimologin = null,
        #[Field(
            Field: 'usu_intentologin',
            Type: DbType::UnsignedInt,
            Default: 0
        )]
        public Field|int|null $usu_intentologin = null,
        #[Field(
            Field: 'usu_estado',
            Type: DbType::UnsignedInt,
            Default: 1,
            Filter: 1
        )]
        public Field|int|null $usu_estado = null
    ) {
    }
}
