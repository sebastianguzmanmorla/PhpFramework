<?php

namespace PhpFramework\Attributes;

use Attribute;
use PhpFramework\Hashids;
use PhpFramework\Request\Enum\ContentType;
use PhpFramework\Request\Enum\Method;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Hashid extends Parameter
{
    public function __construct(
        ?string $Name = null,
        ?Method $Method = Method::GET,
        ?ContentType $ContentType = null
    ) {
        parent::__construct($Name, $Method, $ContentType);
    }

    public function Value(?string $Name = null): array|int|null
    {
        $Value = parent::Value($Name);
        $DecodedValue = $Value !== null ? Hashids::Decode($Value) : null;

        return $DecodedValue !== null && count($DecodedValue) === 1 ? $DecodedValue[0] : $DecodedValue;
    }
}
