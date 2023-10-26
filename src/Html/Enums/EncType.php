<?php

namespace PhpFramework\Html\Enums;

enum EncType: string
{
    case Default = 'application/x-www-form-urlencoded';
    case Multipart = 'multipart/form-data';
}
