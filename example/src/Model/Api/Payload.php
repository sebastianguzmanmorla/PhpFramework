<?php

namespace Model\Api;

use DateTime;

class Payload extends \PhpFramework\Jwt\Payload
{
    public function __construct(
        public ?User $user = null
    ) {
        $Issued = new DateTime();
        $ValidUntil = new DateTime('+3 months');

        parent::__construct(
            sub: $user?->id_usuario,
            name: $user?->usu_nombre . ' ' . $user?->usu_apellido,
            email: $user?->usu_mail,
            iat: $Issued,
            nbf: $Issued,
            exp: $ValidUntil
        );
    }
}
