<?php

namespace Environment;

class Development extends Config
{
    public function __construct()
    {
        parent::__construct(
            Hostname: 'database',
            Port: 3306,
            Username: 'framework',
            Password: 'zag4K4wC542xp7z7',
            Database: 'framework',
            JwtSecret: 'f4c4d3m1c4',
            Debug: true,
            HashidsSalt: 'f4c4d3m1c4',
            SessionName: 'framework-example'
        );
    }
}
