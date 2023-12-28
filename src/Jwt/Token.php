<?php

namespace PhpFramework\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class Token
{
    private static string $Secret;

    private static string $Algorithm = 'HS256';

    public static function Initialize(string $Secret): void
    {
        static::$Secret = $Secret;
    }

    public static function Encode(Payload $Payload): string
    {
        return JWT::encode((array) $Payload, static::$Secret, static::$Algorithm);
    }

    public static function Decode(string $Token): array|false
    {
        try {
            return json_decode(json_encode(JWT::decode($Token, new Key(static::$Secret, static::$Algorithm))), true);
        } catch (Throwable $ex) {
            return false;
        }
    }
}
