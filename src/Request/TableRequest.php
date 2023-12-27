<?php

namespace PhpFramework\Request;

use PhpFramework\Attributes\Parameter;

class TableRequest
{
    #[Parameter('columns')]
    public array $Columns = [];

    #[Parameter('order')]
    public array $Order = [];

    #[Parameter('draw')]
    public int $Draw = 0;

    #[Parameter('start')]
    public int $Start = 0;

    #[Parameter('length')]
    public int $Length = 10;
}
