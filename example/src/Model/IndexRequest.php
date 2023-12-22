<?php

namespace Model;

use PhpFramework\Attributes\Hashid;
use PhpFramework\Attributes\Parameter;
use PhpFramework\Request\JsonRequest;
use PhpFramework\Request\Method;

class IndexRequest extends JsonRequest
{
    #[Parameter(Method::GET)]
    public int $GetId;

    #[Hashid(Method::GET)]
    public int $HashId;

    public ?string $Name;
}
