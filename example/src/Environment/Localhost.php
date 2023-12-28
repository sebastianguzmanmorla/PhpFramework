<?php

namespace Environment;

class Localhost extends Config
{
    public function __construct()
    {
        parent::__construct(
            Hostname: getenv('DATABASE_HOST'),
            Port: (int) getenv('DATABASE_PORT'),
            Username: getenv('DATABASE_USER'),
            Password: getenv('DATABASE_PASSWORD'),
            Database: getenv('DATABASE_DATABASE'),
            JwtSecret: getenv('JWT_SECRET'),
            Debug: true,
            HashidsSalt: getenv('HASHIDS_SALT'),
            SessionName: 'framework-example'
        );
    }
}
