<?php

namespace Controllers\Api;

use Database\Framework\TipoUsuario as DbTipoUsuario;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Request\Enum\Method;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Interface\IResponse;
use PhpFramework\Response\Json\ErrorResponse as ErrorJsonResponse;
use PhpFramework\Response\Json\Response as JsonResponse;
use PhpFramework\Response\Json\ValidationResponse as JsonValidationResponse;
use PhpFramework\Route;

class TipoUsuario extends Controller
{
    #[Singleton]
    private \Database\Framework $Database;

    #[Route('api/TipoUsuario', Method::GET)]
    public function Get(
        int $id_tipousuario
    ): IResponse {
        $TipoUsuario_rs = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $id_tipousuario && $x->tus_estado == 1)
            ->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorJsonResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        return $TipoUsuario_rs->current();
    }

    #[Route('api/TipoUsuario', Method::POST)]
    public function Post(
        DbTipoUsuario $Request
    ): IResponse {
        $Response = new JsonValidationResponse();

        if (!$Response->Validate($Request, $this->Database, true)) {
            return $Response;
        }

        $this->Database->TipoUsuario->Insert($Request);

        return $Request;
    }

    #[Route('api/TipoUsuario', Method::PUT)]
    public function Put(
        DbTipoUsuario $Request
    ): IResponse {
        $Response = new JsonValidationResponse();

        if (!$Response->Validate($Request, $this->Database)) {
            return $Response;
        }

        $TipoUsuario_set = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $Request->id_tipousuario);

        $TipoUsuario_rs = $TipoUsuario_set->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorJsonResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        $TipoUsuario_set->Update($Request);

        return new JsonResponse(StatusCode::Ok);
    }

    #[Route('api/TipoUsuario', Method::DELETE)]
    public function Delete(
        int $id_tipousuario
    ): IResponse {
        $TipoUsuario_set = $this->Database->TipoUsuario
            ->Where(fn (DbTipoUsuario $x) => $x->id_tipousuario == $id_tipousuario);

        $TipoUsuario_rs = $TipoUsuario_set->Select();

        if ($TipoUsuario_rs->EOF()) {
            return new ErrorJsonResponse(StatusCode::NotFound, 'Tipo de Usuario no encontrado');
        }

        $TipoUsuario_set->Update(new DbTipoUsuario(
            tus_estado: 0
        ));

        return new JsonResponse(StatusCode::Ok);
    }
}
