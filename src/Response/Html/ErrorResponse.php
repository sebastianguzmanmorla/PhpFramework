<?php

namespace PhpFramework\Response\Html;

use PhpFramework\Html\Components\Alert;
use PhpFramework\Html\Enums\AlertType;
use PhpFramework\Response\Enum\StatusCode;

class ErrorResponse extends ViewResponse
{
    public ?string $Title = null;

    public ?string $Icon = null;

    public array $Errors;

    public function __construct(
        public StatusCode $StatusCode = StatusCode::InternalServerError,
        string ...$Errors
    ) {
        parent::__construct();

        $this->Errors = $Errors;
    }

    public function Initialize(): void
    {
        $this->Title = 'Error';
        $this->Icon = 'fa fa-exclamation-triangle fa-fw';
    }

    public function Body(): void
    {
        foreach ($this->Errors as $Error) {
            $this->Alerts->AddAlert(new Alert(AlertType::Danger, $Error));
        }

        echo $this->Alerts;
    }
}
