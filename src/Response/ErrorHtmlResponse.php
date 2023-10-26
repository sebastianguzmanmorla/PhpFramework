<?php

namespace PhpFramework\Response;

use PhpFramework\Html\Components\Alert;
use PhpFramework\Html\Enums\AlertType;

class ErrorHtmlResponse extends HtmlResponse
{
    public array $Errors;

    public function __construct(StatusCode $StatusCode = StatusCode::InternalServerError, string ...$Errors)
    {
        parent::__construct($StatusCode);
        $this->Title = 'Error';
        $this->Errors = $Errors;
    }

    public function Init(): void
    {
    }

    public function Body(): void
    {
        foreach ($this->Errors as $Error) {
            $this->Alerts->AddAlert(new Alert(AlertType::Danger, $Error));
        }

        echo $this->Alerts;
    }
}
