<?php

use PhpFramework\Attributes\Singleton;
use PhpFramework\Database\Helpers\SqlFormatter;

require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_extensions('.php');
spl_autoload_register();

Environment\Config::Initialize();

$Database = Singleton::Get(\Database\Framework::class);

foreach ($Database->Tables as $Table) {
    $CreateSyntax = $Table->CreateSyntax();
    echo SqlFormatter::format($CreateSyntax, true);
    $Database->Execute($CreateSyntax);
}
