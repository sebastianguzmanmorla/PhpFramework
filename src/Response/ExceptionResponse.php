<?php

namespace PhpFramework\Response;

use Throwable;

class ExceptionResponse extends ErrorResponse
{
    public function __construct(StatusCode $StatusCode, Throwable $Exception)
    {
        $this->StatusCode = $StatusCode;
        if (isset($_SESSION['Usuario']['id_tipousuario']) && $_SESSION['Usuario']['id_tipousuario'] == 1) {
            $this->Errors = [$Exception->getMessage(), var_export($Exception->getTraceAsString(), true)];
        } else {
            $this->Errors = [$Exception->getMessage(), var_export($Exception->getTraceAsString(), true)];
        }
    }
}
