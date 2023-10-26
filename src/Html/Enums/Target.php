<?php

namespace PhpFramework\Html\Enums;

enum Target: string
{
    case Blank = '_blank';
    case Parent = '_parent';
    case Self = '_self';
    case Top = '_top';
}
