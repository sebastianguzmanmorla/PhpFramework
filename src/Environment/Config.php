<?php

namespace PhpFramework\Environment;

use PhpFramework\Hashids;
use PhpFramework\Jwt\Token;
use PhpFramework\Router;

class Config
{
    private static self $Current;

    public function __construct(
        public bool $Debug,
        public string $HashidsSalt,
        public string $JwtSecret,
        public string $SessionName,
        public int $SessionLifetime = 86400,
        public string $SessionPath = '/',
        public ?string $SessionDomain = null,
        public bool $SessionSecure = false,
        public bool $SessionHttpOnly = false
    ) {
    }

    public static function Current(): static
    {
        return self::$Current;
    }

    public static function Initialize(?self $Config = null): void
    {
        self::$Current = $Config ?? match (getenv('APP_ENV') ?? 'prd') {
            default => new self(
                Debug: false,
                HashidsSalt: getenv('HASHIDS_SALT'),
                JwtSecret: getenv('JWT_SECRET'),
                SessionName: 'framework'
            )
        };

        header_remove('X-Powered-By');

        if (getenv('APP_ENV') == 'local') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            ini_set('xdebug.var_display_max_depth', 5);
            ini_set('xdebug.var_display_max_children', -1);
            ini_set('xdebug.var_display_max_data', -1);
            error_reporting(E_ALL);
        }

        session_name(self::$Current->SessionName);

        session_set_cookie_params(
            self::$Current->SessionLifetime,
            self::$Current->SessionPath,
            self::$Current->SessionDomain,
            self::$Current->SessionSecure,
            self::$Current->SessionHttpOnly
        );

        session_start();

        Hashids::Initialize(self::$Current->HashidsSalt);

        Token::Initialize(self::$Current->JwtSecret);
    }

    public static function Logout(): void
    {
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public static function Process(): ?string
    {
        return Router::Process()->Response();
    }
}
