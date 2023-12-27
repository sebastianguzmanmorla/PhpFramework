<?php

namespace PhpFramework\Request\Enum;

enum Method: string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case PATCH = 'PATCH';

    public static function ReadRequest(): static
    {
        $Method = $_SERVER['REQUEST_METHOD'] ?? null;

        if ($Method == null) {
            return self::GET;
        }

        return match ($Method) {
            self::GET->value => self::GET,
            self::HEAD->value => self::HEAD,
            self::POST->value => self::POST,
            self::PUT->value => self::PUT,
            self::DELETE->value => self::DELETE,
            self::CONNECT->value => self::CONNECT,
            self::OPTIONS->value => self::OPTIONS,
            self::TRACE->value => self::TRACE,
            self::PATCH->value => self::PATCH,
            default => self::GET,
        };
    }
}
