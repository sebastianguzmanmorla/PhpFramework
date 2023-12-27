<?php

namespace PhpFramework\Request;

use ReflectionNamedType;

interface IRequest
{
    public function RequestProcess(
        ?ReflectionNamedType $RequestType = null,
        ?string $RequestParameter = null
    ): void;
}
