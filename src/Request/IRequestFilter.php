<?php

namespace PhpFramework\Request;

use Attribute;
use PhpFramework\Response\IResponse;

#[Attribute(Attribute::TARGET_METHOD)]
interface IRequestFilter
{
    public function Filter(): ?IResponse;
}
