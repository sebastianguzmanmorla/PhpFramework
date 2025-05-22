<?php

namespace PhpFramework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Singleton
{
    public static array $vars = [];

    public function __construct(
        public ?string $Interface = null
    ) {
    }

    public static function Set(object &$Singleton, ?string $Interface = null): void
    {
        static::$vars[$Interface ?? $Singleton::class] = &$Singleton;
    }

    public static function &Get(string $Type): ?object
    {
        $var = null;
        if (static::isset($Type)) {
            $var = &static::$vars[$Type];
        }

        return $var;
    }

    private static function isset(string $Type): bool
    {
        return isset(static::$vars[$Type]);
    }
}
