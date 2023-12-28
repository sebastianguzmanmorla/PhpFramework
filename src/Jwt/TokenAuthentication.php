<?php

namespace PhpFramework\Jwt;

use Attribute;
use PhpFramework\Request\IRequestFilter;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\ErrorResponse;
use PhpFramework\Response\Interface\IResponse;

#[Attribute(Attribute::TARGET_METHOD)]
abstract class TokenAuthentication implements IRequestFilter
{
    protected static ?Payload $Payload = null;

    public function Decode(string $Token): void
    {
        static::$Payload = Payload::Decode($Token);
    }

    public function Valid(): bool
    {
        return static::$Payload !== null;
    }

    public static function Payload(): ?Payload
    {
        return static::$Payload;
    }

    final public function Filter(): ?IResponse
    {
        $Authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if ($Authorization == null) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Authorization header not found');
        }

        $Authorization = explode(' ', $Authorization);

        if (count($Authorization) != 2 || $Authorization[0] != 'Bearer') {
            return new ErrorResponse(StatusCode::Unauthorized, 'Authorization header is invalid');
        }

        $Token = $Authorization[1];

        static::Decode($Token);

        if (!static::Valid(static::$Payload)) {
            return new ErrorResponse(StatusCode::Unauthorized, 'Authorization token is invalid');
        }

        return null;
    }
}
