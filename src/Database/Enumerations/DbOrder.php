<?php

namespace PhpFramework\Database\Enumerations;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbValue;

enum DbOrder: string
{
    case Asc = 'ASC';
    case Desc = 'DESC';

    public static function Asc(Field $Field): DbValue
    {
        return new DbValue(Field: $Field, Order: self::Asc);
    }

    public static function Desc(Field $Field): DbValue
    {
        return new DbValue(Field: $Field, Order: self::Desc);
    }
}
