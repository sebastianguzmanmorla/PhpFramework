<?php

namespace PhpFramework\Response;

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
