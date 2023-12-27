<?php

namespace PhpFramework\Response\Json;

use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Interface\IResponse;

class Response implements IResponse
{
    protected StatusCode $StatusCode = StatusCode::Ok;

    public function __construct(StatusCode $StatusCode = StatusCode::Ok)
    {
        $this->StatusCode = $StatusCode;
    }

    public function Response(): ?string
    {
        header('Content-Type: application/json');
        http_response_code($this->StatusCode->value);

        return json_encode($this);
    }
}
