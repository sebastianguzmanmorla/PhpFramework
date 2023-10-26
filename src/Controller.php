<?php

namespace PhpFramework;

use ReflectionClass;
use ReflectionMethod;

class Controller
{
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
