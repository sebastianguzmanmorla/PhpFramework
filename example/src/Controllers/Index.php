<?php

namespace Controllers;

use Database\Framework\TipoUsuario;
use Database\Framework\Usuario;
use DateTime;
use Environment\Config;
use Model\IndexRequest;
use Model\IndexResponse;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Hashids;
use PhpFramework\Html\Components\Alert;
use PhpFramework\Html\Enums\AlertType;
use PhpFramework\Request\Enum\Method;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Html\ErrorResponse as ErrorHtmlResponse;
use PhpFramework\Response\Interface\IResponse;
use PhpFramework\Response\Json\Response as JsonResponse;
use PhpFramework\Response\RedirectResponse;
use PhpFramework\Route;
use SensitiveParameter;

class Index extends Controller
{
    private const LoginLockout = 600;
    private const LoginLimit = 3;

    #[Singleton]
    private \Database\Framework $Database;

    #[Route('Index')]
    public function Index(): RedirectResponse
    {
        return new RedirectResponse(fn (\Controllers\Index $x) => $x->Login());
    }

    #[Route('Login', Method::GET)]
    public function Login(): IResponse
    {
        if (isset($_SESSION['Usuario']['id_usuario'])) {
            return new RedirectResponse(fn (\Controllers\Admin\Index $x) => $x->Index());
        }

        $View = new \Views\Login();

        return $View;
    }

    #[Route('Login', Method::POST)]
    public function LoginPost(
        #[SensitiveParameter]
        ?string $usu_login,
        #[SensitiveParameter]
        ?string $usu_pass
    ): IResponse {
        if (isset($_SESSION['Usuario']['id_usuario'])) {
            return new RedirectResponse(fn (\Controllers\Admin\Index $x) => $x->Index());
        }

        $View = new \Views\Login();

        $View->Login->Value = $usu_login;

        if (null === $usu_login || null === $usu_pass) {
            $View->StatusCode = StatusCode::Unauthorized;
            $View->Alerts->AddAlert(new Alert(AlertType::Danger, 'Datos incorrectos'));

            return $View;
        }

        $usuario_rs = $this->Database->Usuario
            ->InnerJoin(fn (TipoUsuario $x, Usuario $y) => $x->id_tipousuario == $y->id_tipousuario)
            ->Where(fn (Usuario $x) => $x->usu_login == $usu_login && $x->usu_estado == 1)
            ->Select();

        if (!$usuario_rs->EOF()) {
            $usu_ultimologin = $usuario_rs->Usuario->usu_ultimologin == null ? strtotime('NOW') : $usuario_rs->Usuario->usu_ultimologin->getTimestamp();

            $usuario_set = $this->Database->Usuario
                ->Where(fn (Usuario $x) => $x->id_usuario == $usuario_rs->Usuario->id_usuario);

            if (($usuario_rs->Usuario->usu_intentologin >= static::LoginLimit) && ((time() - $usu_ultimologin) < static::LoginLockout)) {
                $View->Alerts->AddAlert(new Alert(AlertType::Danger, 'Datos incorrectos'));
            } elseif (!password_verify($usu_pass, $usuario_rs->Usuario->usu_pass)) {
                $usuario = new Usuario();

                $usuario->usu_ultimologin = new DateTime();

                if ((time() - $usu_ultimologin) > static::LoginLockout) {
                    $usuario->usu_intentologin = 1;
                } else {
                    $usuario->usu_intentologin = $usuario_rs->Usuario->usu_intentologin + 1;
                }

                $usuario_set->Update($usuario);

                $View->Alerts->AddAlert(new Alert(AlertType::Danger, 'Datos incorrectos'));
            } else {
                $usuario = new Usuario();

                $usuario->usu_ultimologin = new DateTime();
                $usuario->usu_intentologin = 0;

                $usuario_set->Update($usuario);

                $LOGIN_GET = $_SESSION['LOGIN_GET'] ?? [];

                session_regenerate_id(true);

                $_SESSION = json_decode(json_encode($usuario_rs->current()), true);

                //InsertarLog($usuario_rs->id_usuario, ['REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'], 'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT']]);

                return new RedirectResponse(fn (\Controllers\Admin\Index $x) => $x->Index());
            }
        } else {
            $View->Alerts->AddAlert(new Alert(AlertType::Danger, 'Datos incorrectos'));
        }

        return $View;
    }

    #[Route('ModificarPassword')]
    public function ModificarPassword(): IResponse
    {
        if (!isset($_SESSION['Usuario']['id_usuario'])) {
            return new RedirectResponse(fn (Index $x) => $x->Index());
        }

        $id_usuario = $_SESSION['Usuario']['id_usuario'];

        $View = new \Views\ModificarPassword();

        $Usuario_rs = $this->Database->Usuario
            ->InnerJoin(fn (TipoUsuario $x, Usuario $y) => $x->id_tipousuario == $y->id_tipousuario)
            ->Where(fn (Usuario $x) => $x->id_usuario == $id_usuario && $x->usu_estado == 1)
            ->Select();

        if ($Usuario_rs->EOF()) {
            return new ErrorHtmlResponse(StatusCode::NotFound, 'Usuario no encontrado');
        }

        $DbItem = $Usuario_rs->current();

        $View->Usuario = $DbItem->Usuario;
        $View->TipoUsuario = $DbItem->TipoUsuario;

        return $View;
    }

    #[Route('ModificarPassword', Method::POST)]
    public function ModificarPasswordPost(
        ?string $usu_pass = null
    ): IResponse {
        if (!isset($_SESSION['Usuario']['id_usuario'])) {
            return new RedirectResponse(fn (Index $x) => $x->Index());
        }

        $id_usuario = $_SESSION['Usuario']['id_usuario'];

        $View = new \Views\ModificarPassword();

        $Usuario_set = $this->Database->Usuario
            ->InnerJoin(fn (TipoUsuario $x, Usuario $y) => $x->id_tipousuario == $y->id_tipousuario)
            ->Where(fn (Usuario $x) => $x->id_usuario == $id_usuario && $x->usu_estado == 1);

        $Usuario_rs = $Usuario_set->Select();

        if ($Usuario_rs->EOF()) {
            return new ErrorHtmlResponse(StatusCode::NotFound, 'Usuario no encontrado');
        }

        $DbItem = $Usuario_rs->current();

        $View->Usuario = $DbItem->Usuario;
        $View->TipoUsuario = $DbItem->TipoUsuario;

        $View->usu_pass->Value = $usu_pass;

        if ($View->Validate()) {
            $View->Usuario->usu_pass = password_hash($usu_pass, PASSWORD_BCRYPT);

            $Usuario_set->Update($View->Usuario);

            $View->Alerts->AddAlert(new Alert(AlertType::Success, 'Contraseña modificada'));
        }

        $View->usu_pass->Value = null;

        return $View;
    }

    #[Route('Recuperar')]
    public function Recuperar(): IResponse
    {
        $View = new \Views\Recuperar();

        return $View;
    }

    #[Route('Logout')]
    public function Logout(): RedirectResponse
    {
        Config::Logout();

        return new RedirectResponse(fn (\Controllers\Index $x) => $x->Index());
    }

    /*
      Example:
      curl --location 'http://localhost/?route=RestExample&GetId=10&HashId=j1mlyqAyZWew' \
      --header 'Content-Type: application/json' \
      --data '{"Name": "asdf"}'
     */
    #[Route('RestExample', Method::POST)]
    public function IndexPost(IndexRequest $Request): JsonResponse
    {
        $Response = new IndexResponse(
            GetId: $Request->GetId,
            GetIdHash: Hashids::Encode($Request->GetId),
            Id: $Request->HashId,
            Name: $Request->Name
        );

        return $Response;
    }
}
