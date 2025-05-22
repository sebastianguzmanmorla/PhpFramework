<?php

namespace PhpFramework\Response;

use PhpFramework\Environment\Config;
use PhpFramework\Response\Enum\StatusCode;
use Throwable;

class ExceptionResponse extends ErrorResponse
{
    public function __construct(Throwable $Exception, StatusCode $StatusCode = StatusCode::InternalServerError)
    {
        $this->StatusCode = $StatusCode;

        if (Config::Current()->Debug) {
            $this->Errors = [
                sprintf(
                    '%s(%s): %s',
                    $Exception->getFile(),
                    $Exception->getLine(),
                    $Exception->getMessage(),
                ), ...$this->FormatTrace($Exception->getTrace())];
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
                !empty($Value['args']) ? (@json_encode($Value['args']) ?? '') : ''
            );
        }

        return $Array;
    }
}
