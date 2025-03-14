<?php

namespace Environment;

use PhpFramework\Attributes\Singleton;
use PhpFramework\Database\Connection\MySql;
use PhpFramework\Environment\Config as FrameworkConfig;

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
        string $JwtSecret,
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
            JwtSecret: $JwtSecret,
            SessionName: $SessionName,
            SessionLifetime: $SessionLifetime,
            SessionPath: $SessionPath,
            SessionDomain: $SessionDomain,
            SessionSecure: $SessionSecure,
            SessionHttpOnly: $SessionHttpOnly
        );
    }

    public static function Initialize(?FrameworkConfig $Config = null): void
    {
        parent::Initialize($Config ?? match (getenv('APP_ENV') ?? 'prd') {
            'local' => new Localhost(),
            'dev' => new Development(),
            default => new static(
                Hostname: getenv('DATABASE_HOST'),
                Port: (int) getenv('DATABASE_PORT'),
                Username: getenv('DATABASE_USER'),
                Password: getenv('DATABASE_PASSWORD'),
                Database: getenv('DATABASE_DATABASE'),
                JwtSecret: getenv('JWT_SECRET'),
                Debug: false,
                HashidsSalt: getenv('HASHIDS_SALT'),
                SessionName: 'framework-example'
            )
        });

        $Database = \Database\Framework::Initialize(new MySql(
            Hostname: static::Current()->Hostname,
            Port: static::Current()->Port,
            Username: static::Current()->Username,
            Password: static::Current()->Password,
            Database: static::Current()->Database
        ));

        Singleton::Add($Database);
    }
}
