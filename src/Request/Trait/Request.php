<?php

namespace PhpFramework\Request\Trait;

use PhpFramework\Request\Enum\ContentType;
use PhpFramework\Request\Enum\Method;

trait Request
{
    public ?Method $Method = null;

    public ?ContentType $ContentType = null;

    public function Request(): ?array
    {
        return static::GetRequest($this->Method, $this->ContentType);
    }

    public static function GetRequest(?Method $Method = null, ?ContentType $ContentType = null): ?array
    {
        $Method ??= Method::ReadRequest();
        $ContentType ??= ContentType::ReadRequest();

        return match ($Method) {
            Method::GET, Method::DELETE => $_GET,
            default => match ($ContentType) {
                ContentType::FormData, ContentType::FormUrlEncoded => $_POST,
                ContentType::Json => json_decode(file_get_contents('php://input'), true),
                default => null,
            },
        };
    }
}
