<?php

namespace PhpFramework\Attributes;

use Attribute;
use PhpFramework\Request\Method;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Parameter
{
    public function __construct(
        public ?Method $Method = null
    ) {
    }

    public function Method(): mixed
    {
        switch ($this->Method) {
            case Method::GET:
                return $_GET;

                break;
            case Method::POST:
                return $_POST;

                break;
            default:
                return null;

                break;
        }
    }

    public function ParameterValue(string $ParameterName)
    {
        $Value = isset($this->Method()[$ParameterName]) ? $this->Method()[$ParameterName] : null;

        return $Value;
    }
}
