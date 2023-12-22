<?php

namespace Model;

use PhpFramework\Response\JsonResponse;

class IndexResponse extends JsonResponse
{
    public function __construct(
        public ?int $GetId = null,
        public ?string $GetIdHash = null,
        public ?int $Id = null,
        public ?string $Name = null
    ) {
    }
}
