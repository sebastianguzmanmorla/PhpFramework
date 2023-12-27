<?php

namespace Model;

use PhpFramework\Attributes\Hashid;
use PhpFramework\Attributes\Parameter;
use PhpFramework\Request\Enum\Method;

class IndexRequest
{
    #[Parameter(Name: 'GetId', Method: Method::GET)]
    public int $GetId;

    #[Hashid(Name: 'HashId', Method: Method::GET)]
    public int $HashId;

    public ?string $Name;
}
