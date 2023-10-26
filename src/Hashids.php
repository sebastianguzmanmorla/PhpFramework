<?php

namespace PhpFramework;

use SensitiveParameter;

class Hashids
{
    public const IdParameter = 'id';

    private static \Hashids\Hashids $Hashids;

    public static function Init(#[SensitiveParameter] string $Salt): void
    {
        static::$Hashids = new \Hashids\Hashids($Salt, 12);
    }

    public static function Encode(...$numbers): string
    {
        return static::$Hashids->encode($numbers);
    }

    public static function Decode(string $hash): array
    {
        return static::$Hashids->decode($hash);
    }
}
