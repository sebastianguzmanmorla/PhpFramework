<?php

namespace Model\Dashboard;

use PhpFramework\Request\JsonRequest;

class IndexRequest extends JsonRequest
{
    public int $GetId;

    public int $Id;

    public ?string $Name;
}
