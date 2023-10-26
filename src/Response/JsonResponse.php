<?php

namespace PhpFramework\Response;

class JsonResponse implements IResponse
{
    public StatusCode $StatusCode;

    public array $Message = [];

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
