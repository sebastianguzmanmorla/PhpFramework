<?php

namespace PhpFramework\Html\Components;

class Alerts
{
    private array $Alerts = [];

    public function AddAlert(Alert ...$Alert): void
    {
        array_push($this->Alerts, ...$Alert);
    }

    public function __toString(): string
    {
        $Result = '';
        foreach ($this->Alerts as $Alert) {
            $Result .= $Alert;
        }

        return $Result;
    }
}
