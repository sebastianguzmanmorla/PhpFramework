<?php

namespace PhpFramework\Response;

use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Html\ErrorResponse as ErrorHtmlResponse;
use PhpFramework\Response\Interface\IResponse;
use PhpFramework\Response\Json\ErrorResponse as ErrorJsonResponse;

class ErrorResponse implements IResponse
{
    public StatusCode $StatusCode;

    public array $Errors;

    public function __construct(StatusCode $StatusCode = StatusCode::InternalServerError, string ...$Errors)
    {
        $this->StatusCode = $StatusCode;
        $this->Errors = $Errors;
    }

    public function Response(): ?string
    {
        $Response = null;

        if (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) {
            $Response = new ErrorJsonResponse($this->StatusCode, ...$this->Errors);
        } else {
            $Response = new ErrorHtmlResponse($this->StatusCode, ...$this->Errors);
        }

        return $Response->Response();
    }
}
