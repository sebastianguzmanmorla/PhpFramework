<?php

namespace Environment;

use Model\Layout\HtmlResponse;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Controller;
use PhpFramework\Database\Connection\MySql;
use PhpFramework\Environment\Config as FrameworkConfig;
use PhpFramework\Layout\Bootstrap\Admin as AdminBootstrap;
use PhpFramework\Layout\Bootstrap\Login as LoginBootstrap;

class Config extends FrameworkConfig
{
    public static string $Project = 'PhpFramework Example';

    public static string $Author = 'Sebastián Guzmán';

    public function __construct(
        public string $Hostname,
        public int $Port,
        public string $Username,
        public string $Password,
        public string $Database,
        bool $Debug,
        string $HashidsSalt,
        string $SessionName,
        int $SessionLifetime = 86400,
        string $SessionPath = '/',
        ?string $SessionDomain = null,
        bool $SessionSecure = false,
        bool $SessionHttpOnly = false
    ) {
        parent::__construct(
            Debug: $Debug,
            HashidsSalt: $HashidsSalt,
            SessionName: $SessionName,
            SessionLifetime: $SessionLifetime,
            SessionPath: $SessionPath,
            SessionDomain: $SessionDomain,
            SessionSecure: $SessionSecure,
            SessionHttpOnly: $SessionHttpOnly
        );
    }

    public static function Initialize(): void
    {
        parent::Initialize();

        $Database = \Database\Framework::Initialize(new MySql(
            Hostname: self::Environment()->Hostname,
            Port: self::Environment()->Port,
            Username: self::Environment()->Username,
            Password: self::Environment()->Password,
            Database: self::Environment()->Database
        ));

        Singleton::Add($Database);

        Controller::AutoLoad(realpath('./Controllers'));

        HtmlResponse::InitializeDefault(
            Project: self::$Project,
            Author: self::$Author,
            Layout: isset($_SESSION['Usuario']) ? new AdminBootstrap() : new LoginBootstrap()
        );
    }

    public static function Environment(?string $Environment = null): self
    {
        $Environment ??= getenv('APP_ENV') ?? 'prd';

        return match ($Environment) {
            'local' => new Localhost(),
            'dev' => new Development(),
            default => new self(
                Hostname: getenv('DATABASE_HOST'),
                Port: (int) getenv('DATABASE_PORT'),
                Username: getenv('DATABASE_USER'),
                Password: getenv('DATABASE_PASSWORD'),
                Database: getenv('DATABASE_DATABASE'),
                Debug: false,
                HashidsSalt: getenv('HASHIDS_SALT'),
                SessionName: 'framework-example'
            )
        };
    }
}
