<?php

namespace PhpFramework;

use PhpFramework\Response\StatusCode;
use Throwable;

class Exception extends \Exception
{
    public StatusCode $StatusCode;

    public function __construct(string $Message = '', StatusCode $StatusCode = StatusCode::InternalServerError, int $Code = 0, ?Throwable $Previous = null)
    {
        $this->StatusCode = $StatusCode;
        parent::__construct($Message, $Code, $Previous);
    }
}
