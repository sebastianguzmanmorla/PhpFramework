<?php

namespace Model;

use PhpFramework\Response\Json\Response;

class IndexResponse extends Response
{
    public function __construct(
        public ?int $GetId = null,
        public ?string $GetIdHash = null,
        public ?int $Id = null,
        public ?string $Name = null
    ) {
    }
}
