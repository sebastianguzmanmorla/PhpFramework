<?php

namespace PhpFramework;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionMethod;

class Controller
{
    public static function AutoLoad(string $ControllerFolder): void
    {
        $Files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($ControllerFolder, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($Files as $File) {
            if ($File->getExtension() == 'php') {
                require_once $File->getPathname();
                $Classes = get_declared_classes();
                $Class = end($Classes);
                $ControllerClass = new ReflectionClass($Class);
                if ($ControllerClass->isSubclassOf(static::class)) {
                    $ControllerClass->getMethod('Register')->invoke(null);
                }
            }
        }
    }

    public static function Register(): void
    {
        $reflectionController = new ReflectionClass(static::class);

        foreach ($reflectionController->getMethods() as $reflectionMethod) {
            $RouteAttributes = $reflectionMethod->getAttributes(Route::class);

            foreach ($RouteAttributes as $RouteAttribute) {
                $Route = $RouteAttribute->newInstance();
                Router::$Routes[$Route->Route][$Route->Method->value][Router::Controller] = $reflectionController;
                Router::$Routes[$Route->Route][$Route->Method->value][Router::Method] = new ReflectionMethod($reflectionMethod->class, $reflectionMethod->name);
            }
        }
    }
}
