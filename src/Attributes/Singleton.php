<?php

namespace PhpFramework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Singleton
{
    public static array $vars = [];

    public static function Add(object &$Singleton): void
    {
        static::$vars[get_class($Singleton)] = &$Singleton;
    }

    private static function isset(string $Type): bool
    {
        return isset(static::$vars[$Type]);
    }

    public static function &Get(string $Type): ?object
    {
        $var = null;
        if (static::isset($Type)) {
            $var = &static::$vars[$Type];
        }

        return $var;
    }
}
