<?php

namespace PhpFramework\Database\Enumerations;

use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\DbValue;
use PhpToken;

enum DbWhere: string
{
    case Equal = '=';
    case NotEqual = '<>';
    case MoreThan = '>';
    case MoreEqualThan = '>=';
    case LessThan = '<';
    case LessEqualThan = '<=';
    case In = 'IN';
    case NotIn = 'NOT IN';
    case Like = 'LIKE';
    case NotLike = 'NOT LIKE';
    case IsNull = 'IS NULL';
    case IsNotNull = 'IS NOT NULL';
    case Between = 'BETWEEN';

    public static function Equal(Field $Field, mixed $Value): DbValue
    {
        return new DbValue(Field: $Field, Where: self::Equal, Value: $Value);
    }

    public static function NotEqual(Field $Field, mixed $Value): DbValue
    {
        return new DbValue(Field: $Field, Where: self::NotEqual, Value: $Value);
    }

    public static function MoreThan(Field $Field, mixed $Value): DbValue
    {
        return new DbValue(Field: $Field, Where: self::MoreThan, Value: $Value);
    }

    public static function MoreEqualThan(Field $Field, mixed $Value): DbValue
    {
        return new DbValue(Field: $Field, Where: self::MoreEqualThan, Value: $Value);
    }

    public static function LessThan(Field $Field, mixed $Value): DbValue
    {
        return new DbValue(Field: $Field, Where: self::LessThan, Value: $Value);
    }

    public static function LessEqualThan(Field $Field, mixed $Value): DbValue
    {
        return new DbValue(Field: $Field, Where: self::LessEqualThan, Value: $Value);
    }

    public static function In(Field $Field, array $Values): DbValue
    {
        return new DbValue(Field: $Field, Where: self::In, Value: $Values);
    }

    public static function NotIn(Field $Field, array $Values): DbValue
    {
        return new DbValue(Field: $Field, Where: self::NotIn, Value: $Values);
    }

    public static function Like(Field $Field, mixed $Value): DbValue
    {
        return new DbValue(Field: $Field, Where: self::Like, Value: '%' . $Value . '%');
    }

    public static function NotLike(Field $Field, mixed $Value): DbValue
    {
        return new DbValue(Field: $Field, Where: self::NotLike, Value: '%' . $Value . '%');
    }

    public static function IsNull(Field $Field): DbValue
    {
        return new DbValue(Field: $Field, Where: self::IsNull);
    }

    public static function IsNotNull(Field $Field): DbValue
    {
        return new DbValue(Field: $Field, Where: self::IsNotNull);
    }

    public static function Between(Field $Field, mixed $Start, mixed $End): DbValue
    {
        return new DbValue(Field: $Field, Where: self::Between, Value: [$Start, $End]);
    }

    public static function FromToken(PhpToken $Token): ?DbWhere
    {
        if ($Token->text == '>') {
            return self::MoreThan;
        }
        if ($Token->text == '<') {
            return self::LessThan;
        }
        switch ($Token->id) {
            case T_IS_EQUAL:
                return self::Equal;
            case T_IS_IDENTICAL:
                return self::Equal;
            case T_IS_NOT_EQUAL:
                return self::NotEqual;
            case T_IS_NOT_IDENTICAL:
                return self::NotEqual;
            case T_IS_GREATER_OR_EQUAL:
                return self::MoreEqualThan;
            case T_IS_SMALLER_OR_EQUAL:
                return self::LessEqualThan;
            default:
                return null;
        }
    }
}
