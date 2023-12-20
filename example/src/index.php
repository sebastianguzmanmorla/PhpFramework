<?php

require_once __DIR__ . '/../../vendor/autoload.php';

spl_autoload_extensions('.php');
spl_autoload_register();

Environment\Config::Initialize();

echo Environment\Config::Process();
