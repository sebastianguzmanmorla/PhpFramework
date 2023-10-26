<?php

namespace PhpFramework\Html\Components;

class Scripts
{
    private array $Scripts = [];

    public function Add(Script ...$Script): void
    {
        array_push($this->Scripts, ...$Script);
    }

    public function __toString(): string
    {
        $Result = '';
        foreach ($this->Scripts as $Script) {
            $Result .= $Script;
        }

        return $Result;
    }
}
