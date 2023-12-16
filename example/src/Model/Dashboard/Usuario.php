<?php

namespace Model\Dashboard;

use DateTime;

class Usuario
{
    public function __construct(
        public ?int $id_usuario = null,
        public ?string $usu_nombre = null,
        public ?string $usu_apellido = null,
        public ?DateTime $usu_ultimologin = null
    ) {
    }
}
