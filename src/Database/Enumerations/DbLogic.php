<?php

namespace PhpFramework\Database\Enumerations;

use PhpToken;

enum DbLogic: string
{
    case And = 'AND';
    case Or = 'OR';
    case Comma = ',';

    public static function FromToken(PhpToken $Token): ?DbLogic
    {
        switch ($Token->id) {
            case T_LOGICAL_AND:
                return self::And;

                break;
            case T_BOOLEAN_AND:
                return self::And;

                break;
            case T_LOGICAL_OR:
                return self::Or;

                break;
            case T_BOOLEAN_OR:
                return self::Or;

                break;
            default:
                return null;

                break;
        }
    }
}
