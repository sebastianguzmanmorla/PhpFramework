<?php

namespace PhpFramework\Request\Enum;

enum ContentType: string
{
    case Json = 'application/json';
    case Html = 'text/html';
    case Xml = 'text/xml';
    case Plain = 'text/plain';
    case FormUrlEncoded = 'application/x-www-form-urlencoded';
    case FormData = 'multipart/form-data';

    public static function ReadRequest(): static
    {
        $ContentType = $_SERVER['CONTENT_TYPE'] ?? null;

        if ($ContentType !== null && str_contains($ContentType, ';')) {
            $ContentType = explode(';', $ContentType)[0];
        }

        return match ($ContentType) {
            self::Json->value => self::Json,
            self::Html->value => self::Html,
            self::Xml->value => self::Xml,
            self::Plain->value => self::Plain,
            self::FormUrlEncoded->value => self::FormUrlEncoded,
            self::FormData->value => self::FormData,
            default => self::FormData,
        };
    }
}
