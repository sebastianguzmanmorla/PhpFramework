<?php

namespace PhpFramework\Layout;

abstract class Layout implements ILayout
{
    public static function Self(): static
    {
        return new static();
    }
}
