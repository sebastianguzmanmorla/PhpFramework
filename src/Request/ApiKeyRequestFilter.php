<?php

namespace PhpFramework\Request;

use Attribute;
use PhpFramework\Response\ErrorResponse;
use PhpFramework\Response\IResponse;
use PhpFramework\Response\StatusCode;
use SensitiveParameter;

#[Attribute(Attribute::TARGET_METHOD)]
class ApiKeyRequestFilter implements IRequestFilter
{
    public string $ApiKey;

    public function __construct(#[SensitiveParameter] string $ApiKey)
    {
        $this->ApiKey = $ApiKey;
    }

    public function Filter(): ?IResponse
    {
        if (!isset($_SERVER['HTTP_API_KEY'])) {
            return new ErrorResponse(StatusCode::Unauthorized, 'API Key not found');
        }

        if ($_SERVER['HTTP_API_KEY'] != $this->ApiKey) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Invalid API Key');
        }

        return null;
    }
}
