<?php

namespace PhpFramework\Layout;

use Attribute;
use PhpFramework\Exception;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
class UseLayout
{
    public ILayout $Layout;

    public function __construct(string $Layout)
    {
        $ReflectionClass = new ReflectionClass($Layout);

        if ($ReflectionClass->isSubclassOf(ILayout::class)) {
            $this->Layout = $ReflectionClass->newInstance();
        } else {
            throw new Exception($Layout . ' no es de la clase ' . ILayout::class);
        }
    }
}
