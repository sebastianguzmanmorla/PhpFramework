<?php

namespace PhpFramework\Attributes;

use Attribute;
use PhpFramework\Request\Method;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Parameter
{
    public Method $Method;

    public function __construct(Method $Method = Method::GET)
    {
        $this->Method = $Method;
    }
}
