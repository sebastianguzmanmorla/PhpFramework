<?php

namespace PhpFramework\Html\Components;

class Stylesheets
{
    private array $Stylesheets = [];

    public function __toString(): string
    {
        $Result = '';
        foreach ($this->Stylesheets as $Stylesheet) {
            $Result .= $Stylesheet;
        }

        return $Result;
    }

    public function Add(Stylesheet ...$Stylesheet): void
    {
        array_push($this->Stylesheets, ...$Stylesheet);
    }
}
