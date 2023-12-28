<?php

namespace Model\Api;

use PhpFramework\Response\Json\Response;

class LoginResponse extends Response
{
    public function __construct(
        public ?string $Token = null
    ) {
    }
}
