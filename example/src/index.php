<?php

use Environment\Config;
use Model\Layout\HtmlResponse;
use PhpFramework\Controller;
use PhpFramework\Router;
use PhpFramework\Layout\Bootstrap\Admin as AdminBootstrap;
use PhpFramework\Layout\Bootstrap\Login as LoginBootstrap;

require_once __DIR__ . './../vendor/autoload.php';

spl_autoload_extensions('.php');
spl_autoload_register();

Config::Initialize();

Controller::AutoLoad(realpath('./Controllers'));

HtmlResponse::InitializeDefault(
    Project: Config::$Project,
    Author: Config::$Author,
    Layout: isset($_SESSION['Usuario']) ? new AdminBootstrap() : new LoginBootstrap()
);

echo Router::Process()->Response();
