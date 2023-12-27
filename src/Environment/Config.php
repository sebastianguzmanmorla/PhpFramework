<?php

namespace PhpFramework\Environment;

use PhpFramework\Hashids;
use PhpFramework\Router;

class Config
{
    public static Config $Current;

    public function __construct(
        public bool $Debug,
        public string $HashidsSalt,
        public string $SessionName,
        public int $SessionLifetime = 86400,
        public string $SessionPath = '/',
        public ?string $SessionDomain = null,
        public bool $SessionSecure = false,
        public bool $SessionHttpOnly = false
    ) {
    }

    public static function Initialize(): void
    {
        header_remove('X-Powered-By');

        if (getenv('APP_ENV') == 'local') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            ini_set('xdebug.var_display_max_depth', 5);
            ini_set('xdebug.var_display_max_children', -1);
            ini_set('xdebug.var_display_max_data', -1);
            error_reporting(E_ALL);
        }

        session_name(self::Environment()->SessionName);

        session_set_cookie_params(
            self::Environment()->SessionLifetime,
            self::Environment()->SessionPath,
            self::Environment()->SessionDomain,
            self::Environment()->SessionSecure,
            self::Environment()->SessionHttpOnly
        );

        session_start();

        Hashids::Initialize(self::Environment()->HashidsSalt);
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

    public static function Environment(?string $Environment = null): self
    {
        $Environment ??= getenv('APP_ENV') ?? 'prd';

        return match ($Environment) {
            'local' => new self(
                Debug: true,
                HashidsSalt: getenv('HASHIDS_SALT'),
                SessionName: 'framework',
            ),
            default => new self(
                Debug: false,
                HashidsSalt: getenv('HASHIDS_SALT'),
                SessionName: 'framework',
            )
        };
    }
}
