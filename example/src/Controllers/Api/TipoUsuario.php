<?php

namespace Controllers\Api;

use Database\Framework\TipoUsuario as DbTipoUsuario;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Request\Enum\Method;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Json\ErrorResponse;
use PhpFramework\Response\Json\Response;
use PhpFramework\Response\Json\ValidationResponse;
use PhpFramework\Route;
use Request\TokenAuthentication;

class TipoUsuario extends Controller
{
    #[Singleton]
    private \Database\Framework $Database;

    #[Route('api/TipoUsuario', Method::GET), TokenAuthentication]
    public function Get(): Response
    {
        $TipoUsuario_rs = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->tus_estado == 1)
            ->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        return new Response(Object: $TipoUsuario_rs);
    }

    #[Route('api/TipoUsuario', Method::POST), TokenAuthentication]
    public function Post(
        DbTipoUsuario $Request
    ): Response {
        $Response = new ValidationResponse();

        if (!$Response->Validate($Request, $this->Database, true)) {
            return $Response;
        }

        $this->Database->TipoUsuario->Insert($Request);

        return $Request;
    }

    #[Route('api/TipoUsuario', Method::PUT), TokenAuthentication]
    public function Put(
        DbTipoUsuario $Request
    ): Response {
        $Response = new ValidationResponse();

        if (!$Response->Validate($Request, $this->Database)) {
            return $Response;
        }

        $TipoUsuario_set = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $Request->id_tipousuario);

        $TipoUsuario_rs = $TipoUsuario_set->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        $TipoUsuario_set->Update($Request);

        return new Response(StatusCode::Ok);
    }

    #[Route('api/TipoUsuario', Method::DELETE), TokenAuthentication]
    public function Delete(
        int $id_tipousuario
    ): Response {
        $TipoUsuario_set = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $id_tipousuario);

        $TipoUsuario_rs = $TipoUsuario_set->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        $TipoUsuario_set->Update(new DbTipoUsuario(
            tus_estado: 0
        ));

        return new Response(StatusCode::Ok);
    }
}
