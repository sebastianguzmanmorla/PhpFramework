<?php

namespace PhpFramework;

use Attribute;
use PhpFramework\Request\Enum\Method;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public ?string $Route;

    public Method $Method;

    public static $DefaultRoute = 'Index';

    public static $DefaultMethod = Method::GET;

    public function __construct(string $Route, Method $Method = Method::GET)
    {
        $this->Route = $Route;
        $this->Method = $Method;
    }
}
