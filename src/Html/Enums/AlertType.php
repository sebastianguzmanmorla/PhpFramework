<?php

namespace PhpFramework\Html\Enums;

enum AlertType: string
{
    case Primary = 'alert alert-primary';
    case Secondary = 'alert alert-secondary';
    case Success = 'alert alert-success';
    case Danger = 'alert alert-danger';
    case Warning = 'alert alert-warning';
    case Info = 'alert alert-info';
    case Light = 'alert alert-light';
    case Dark = 'alert alert-dark';
}
