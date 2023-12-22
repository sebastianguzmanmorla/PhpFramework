<?php

namespace PhpFramework\Attributes;

use Attribute;
use PhpFramework\Hashids;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Hashid extends Parameter
{
    public function ParameterValue(string $ParameterName): array|int|null
    {
        $Value = parent::ParameterValue($ParameterName);
        $DecodedValue = $Value !== null ? Hashids::Decode($Value) : null;

        return $DecodedValue !== null && count($DecodedValue) === 1 ? $DecodedValue[0] : $DecodedValue;
    }
}
