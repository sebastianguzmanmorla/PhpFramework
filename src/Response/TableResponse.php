<?php

namespace PhpFramework\Response;

use PhpFramework\Database\DbResourceSet;
use PhpFramework\Request\TableRequest;

class TableResponse extends JsonResponse
{
    public int $recordsTotal;

    public int $recordsFiltered;

    public int $draw;

    public string $query;

    public function __construct(
        public DbResourceSet $data,
        public ?TableRequest $TableRequest = null,
        StatusCode $StatusCode = StatusCode::Ok
    ) {
        $this->draw = $TableRequest?->Draw ?? 1;
        $this->recordsTotal = $data->Total;
        $this->recordsFiltered = $data->Total;
        $this->query = $data->Query->__toString();
        parent::__construct($StatusCode);
    }
}
