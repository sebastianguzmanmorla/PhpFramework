<?php

namespace PhpFramework\Response;

use PhpFramework\Environment\Config;
use PhpFramework\Response\Enum\StatusCode;
use Throwable;

class ExceptionResponse extends ErrorResponse
{
    public function __construct(StatusCode $StatusCode, Throwable $Exception)
    {
        $this->StatusCode = $StatusCode;
        if (Config::Environment()->Debug) {
            $this->Errors = [$Exception->getMessage(), ...$this->FormatTrace($Exception->getTrace())];
        } else {
            $this->Errors = [$Exception->getMessage()];
        }
    }

    private function FormatTrace(array $Trace): array
    {
        $Array = [];
        foreach ($Trace as $Key => $Value) {
            $Array[] = sprintf(
                '#%s %s(%s): %s%s%s(%s)',
                $Key,
                $Value['file'] ?? 'unknown',
                $Value['line'] ?? 'unknown',
                $Value['class'] ?? '',
                $Value['type'] ?? '',
                $Value['function'] ?? 'unknown',
                implode(', ', array_map(fn ($Value) => var_export($Value, true), $Value['args']))
            );
        }

        return $Array;
    }
}
