<?php

namespace PhpFramework\Attributes;

use Attribute;
use PhpFramework\Request\Enum\ContentType;
use PhpFramework\Request\Enum\Method;
use PhpFramework\Request\Trait\Request;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Parameter
{
    use Request;

    public function __construct(
        public ?string $Name = null,
        ?Method $Method = null,
        ?ContentType $ContentType = null
    ) {
        $this->Method = $Method ?? Method::ReadRequest();
        $this->ContentType = $ContentType ?? ContentType::ReadRequest();
    }

    public function Value(?string $Name = null): mixed
    {
        $Name ??= $this->Name;
        $Request = $this->Request();
        $Value = match ($this->ContentType) {
            ContentType::FormData, ContentType::FormUrlEncoded => $Request[$Name] ?? null,
            ContentType::Json => $Request[$Name] ?? null,
            default => null,
        };

        return $Value !== '' ? $Value : null;
    }
}
