<?php

namespace Model\Api;

use PhpFramework\Response\Json\Response;

class User extends Response
{
    public function __construct(
        public int $id_usuario,
        public string $usu_mail,
        public string $usu_rut,
        public string $usu_nombre,
        public string $usu_apellido,
        public int $id_tipousuario,
        public string $tus_nombre,
    ) {
    }
}
