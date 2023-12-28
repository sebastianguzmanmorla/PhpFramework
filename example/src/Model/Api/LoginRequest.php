<?php

namespace Model\Api;

use PhpFramework\Attributes\Validation;
use PhpFramework\Html\Validation\Rules\IsNotNullOrEmpty;
use PhpFramework\Html\Validation\Rules\IsValidEmail;
use PhpFramework\Html\Validation\Rules\IsValidPassword;

class LoginRequest
{
    #[Validation(
        new IsNotNullOrEmpty('El Correo es obligatorio'),
        new IsValidEmail('El Correo no es válido')
    )]
    public ?string $Mail;

    #[Validation(
        new IsNotNullOrEmpty('La Contraseña es obligatoria'),
        new IsValidPassword()
    )]
    public ?string $Password;
}
