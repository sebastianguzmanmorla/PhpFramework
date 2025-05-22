<?php

namespace PhpFramework\Response\Json;

use PhpFramework\Response\Enum\StatusCode;

class ErrorResponse extends Response
{
    public array $Errors;

    public function __construct(
        StatusCode $Status = StatusCode::InternalServerError,
        string ...$Errors
    ) {
        parent::__construct(null, $Status);

        $this->Errors = $Errors;
    }
}
