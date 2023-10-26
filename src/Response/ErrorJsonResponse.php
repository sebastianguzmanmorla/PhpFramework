<?php

namespace PhpFramework\Response;

class ErrorJsonResponse extends JsonResponse
{
    public function __construct(StatusCode $Status = StatusCode::InternalServerError, string ...$Errors)
    {
        parent::__construct($Status);
        $this->Message = $Errors;
    }
}
