<?php

namespace PhpFramework\Response\Json;

use JsonSerializable;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Interface\IResponse;

class Response implements IResponse, JsonSerializable
{
    protected StatusCode $StatusCode = StatusCode::Ok;

    public function __construct(
        protected array|object|null $Object = null,
        StatusCode $StatusCode = StatusCode::Ok,
    ) {
        $this->StatusCode = $StatusCode;
    }

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }

    public function jsonSerialize(): mixed
    {
        return $this->Object ?? $this;
    }

    public function Response(): ?string
    {
        header('Content-Type: application/json');
        http_response_code($this->StatusCode->value);

        return json_encode($this->jsonSerialize());
    }
}
