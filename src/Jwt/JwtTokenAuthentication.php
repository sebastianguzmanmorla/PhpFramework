<?php

namespace PhpFramework\Jwt;

use Attribute;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Request\IAuthentication;
use PhpFramework\Request\IRequestFilter;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\ErrorResponse;
use PhpFramework\Response\Interface\IResponse;
use Throwable;

#[Attribute(Attribute::TARGET_METHOD)]
abstract class JwtTokenAuthentication implements IAuthentication, IRequestFilter
{
    abstract public function Decode(string $Token): Throwable|true;

    abstract public function IsAuthenticated(): bool;

    final public function Filter(): ?IResponse
    {
        Singleton::Set($this, IAuthentication::class);

        $Authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if ($Authorization == null) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Authorization header not found');
        }

        $Authorization = explode(' ', $Authorization);

        if (count($Authorization) != 2 || $Authorization[0] != 'Bearer') {
            return new ErrorResponse(StatusCode::Unauthorized, 'Authorization header is invalid');
        }

        $Result = $this->Decode($Authorization[1]);

        if ($Result instanceof Throwable) {
            return new ErrorResponse(StatusCode::Unauthorized, $Result->getMessage());
        }

        if (!$this->IsAuthenticated()) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Authorization token is invalid');
        }

        return null;
    }
}
