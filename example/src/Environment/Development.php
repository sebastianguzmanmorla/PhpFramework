<?php

namespace Environment;

class Development extends Config
{
    public function __construct()
    {
        parent::__construct(
            SessionName: 'framework',
            Hostname: 'database',
            Port: 3306,
            Username: 'framework',
            Password: 'zag4K4wC542xp7z7',
            Database: 'framework',
            HashidsSalt: 'f4c4d3m1c4'
        );
    }
}
