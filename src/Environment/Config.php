<?php

namespace PhpFramework\Environment;

use PhpFramework\Hashids;
use PhpFramework\Jwt\JwtToken;

class Config
{
    private static self $Current;

    public function __construct(
        public string $Project,
        public string $Author,
        public string $Env,
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

    public static function Current(): self
    {
        return self::$Current;
    }

    public static function Initialize(string $Project, string $Author, ?self $Config = null): void
    {
        self::$Current = $Config ?? new self(
            Project: $Project,
            Author: $Author,
            Env: getenv('APP_ENV'),
            Debug: (bool) (getenv('APP_DEBUG')),
            HashidsSalt: getenv('APP_HASHIDS_SALT'),
            JwtSecret: getenv('APP_JWT_SECRET'),
            SessionName: getenv('APP_NAME')
        );

        header_remove('X-Powered-By');

        if (self::$Current->Debug) {
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

        JwtToken::Initialize(self::$Current->JwtSecret);
    }

    public static function Logout(): void
    {
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
}
