<?php

namespace Environment;

use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Database\Connection\MySql;
use PhpFramework\Hashids;
use PhpFramework\Layout\Bootstrap\Admin as AdminBootstrap;
use PhpFramework\Response\HtmlResponse;
use PhpFramework\Router;

class Config
{
    public static string $Project = 'PhpFramework Example';

    public static string $Author = 'Sebastián Guzmán';

    public function __construct(
        public string $Hostname,
        public int $Port,
        public string $Username,
        public string $Password,
        public string $Database,
        public string $HashidsSalt,
        public string $SessionName = 'phpframework',
        public int $SessionLifetime = 86400,
        public string $SessionPath = '/',
        public ?string $SessionDomain = null,
        public bool $SessionSecure = true,
        public bool $SessionHttpOnly = true
    ) {
    }

    public static function Initialize(): void
    {
        header_remove('X-Powered-By');

        if (getenv('APP_ENV') == 'local') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            ini_set('xdebug.var_display_max_depth', -1);
            ini_set('xdebug.var_display_max_children', -1);
            ini_set('xdebug.var_display_max_data', -1);
            error_reporting(E_ALL);
        }

        session_name(self::Current()->SessionName);

        session_set_cookie_params(
            self::Current()->SessionLifetime,
            self::Current()->SessionPath,
            self::Current()->SessionDomain,
            self::Current()->SessionSecure,
            self::Current()->SessionHttpOnly
        );

        session_start();

        Hashids::Init(self::Current()->HashidsSalt);

        $Database = new \Database\Framework(new MySql(
            Hostname: self::Current()->Hostname,
            Port: self::Current()->Port,
            Username: self::Current()->Username,
            Password: self::Current()->Password,
            Database: self::Current()->Database
        ));

        Singleton::Add($Database);

        Controller::AutoLoad(realpath('./Controllers'));

        HtmlResponse::$DefaultLayout = new AdminBootstrap();
    }

    public static function Process(): ?string
    {
        return Router::Process()->Response();
    }

    public static function Current(?string $Environment = null): self
    {
        $Environment ??= getenv('APP_ENV') ?? 'prd';

        return match ($Environment) {
            'dev' => new Development(),
            default => new self(
                Hostname: getenv('DATABASE_HOST'),
                Port: (int) getenv('DATABASE_PORT'),
                Username: getenv('DATABASE_USER'),
                Password: getenv('DATABASE_PASSWORD'),
                Database: getenv('DATABASE_DATABASE'),
                HashidsSalt: getenv('HASHIDS_SALT')
            )
        };
    }
}
