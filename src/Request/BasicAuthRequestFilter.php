<?php

namespace PhpFramework\Request;

use Attribute;
use PhpFramework\Response\ErrorResponse;
use PhpFramework\Response\IResponse;
use PhpFramework\Response\StatusCode;
use SensitiveParameter;

#[Attribute(Attribute::TARGET_METHOD)]
class BasicAuthRequestFilter implements IRequestFilter
{
    public string $Username;

    public string $Password;

    public function __construct(#[SensitiveParameter] string $Username, #[SensitiveParameter] string $Password)
    {
        $this->Username = $Username;
        $this->Password = $Password;
    }

    public function Filter(): ?IResponse
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Username not found');
        }

        if (!isset($_SERVER['PHP_AUTH_PW'])) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Password not found');
        }

        if ($_SERVER['PHP_AUTH_USER'] != $this->Username || $_SERVER['PHP_AUTH_PW'] != $this->Password) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Invalid Username or Password');
        }

        return null;
    }
}
