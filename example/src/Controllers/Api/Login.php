<?php

namespace Controllers\Api;

use Database\Framework\TipoUsuario;
use Database\Framework\Usuario;
use DateTime;
use Model\Api\LoginRequest;
use Model\Api\LoginResponse;
use Model\Api\Payload;
use Model\Api\User;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Jwt\Token;
use PhpFramework\Request\Enum\Method;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Json\ErrorResponse;
use PhpFramework\Response\Json\Response;
use PhpFramework\Response\Json\ValidationResponse;
use PhpFramework\Route;
use Request\TokenAuthentication;

class Login extends Controller
{
    private const LoginLockout = 600;
    private const LoginLimit = 3;

    #[Singleton]
    private \Database\Framework $Database;

    #[Route('api/PayloadFromToken', Method::GET), TokenAuthentication]
    public function PayloadFromToken(): Response
    {
        $Payload = TokenAuthentication::Payload();

        return new Response(Object: $Payload);
    }

    #[Route('api/UserFromToken', Method::GET), TokenAuthentication]
    public function UserFromToken(): Response
    {
        $User = TokenAuthentication::Payload()->user;

        return $User;
    }

    #[Route('api/Login', Method::POST)]
    public function Post(LoginRequest $Request): Response
    {
        $Response = new ValidationResponse();

        if (!$Response->Validate($Request)) {
            return $Response;
        }

        $usuario_rs = $this->Database->Usuario
            ->InnerJoin(fn (TipoUsuario $x, Usuario $y) => $x->id_tipousuario == $y->id_tipousuario)
            ->Where(fn (Usuario $x) => $x->usu_mail == $Request->Mail && $x->usu_estado == 1)
            ->Select();

        if ($usuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Datos incorrectos');
        }

        $usu_ultimologin = $usuario_rs->Usuario->usu_ultimologin == null ? strtotime('NOW') : $usuario_rs->Usuario->usu_ultimologin->getTimestamp();

        $usuario_set = $this->Database->Usuario
            ->Where(fn (Usuario $x) => $x->id_usuario == $usuario_rs->Usuario->id_usuario);

        if (($usuario_rs->Usuario->usu_intentologin >= static::LoginLimit) && ((time() - $usu_ultimologin) < static::LoginLockout)) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Datos incorrectos');
        }

        if (!password_verify($Request->Password, $usuario_rs->Usuario->usu_pass)) {
            $usuario = new Usuario();

            $usuario->usu_ultimologin = new DateTime();

            if ((time() - $usu_ultimologin) > static::LoginLockout) {
                $usuario->usu_intentologin = 1;
            } else {
                $usuario->usu_intentologin = $usuario_rs->Usuario->usu_intentologin + 1;
            }

            $usuario_set->Update($usuario);

            return new ErrorResponse(StatusCode::Unauthorized, 'Datos incorrectos');
        }

        $usuario = new Usuario();

        $usuario->usu_ultimologin = new DateTime();
        $usuario->usu_intentologin = 0;

        $usuario_set->Update($usuario);

        return new LoginResponse(
            Token: Token::Encode(
                new Payload(
                    new User(
                        id_usuario: $usuario_rs->Usuario->id_usuario,
                        usu_mail: $usuario_rs->Usuario->usu_mail,
                        usu_rut: $usuario_rs->Usuario->usu_rut,
                        usu_nombre: $usuario_rs->Usuario->usu_nombre,
                        usu_apellido: $usuario_rs->Usuario->usu_apellido,
                        id_tipousuario: $usuario_rs->TipoUsuario->id_tipousuario,
                        tus_nombre: $usuario_rs->TipoUsuario->tus_nombre
                    )
                )
            )
        );
    }
}
