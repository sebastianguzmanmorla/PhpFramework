<?php

namespace PhpFramework\Response\Json;

use LiliDb\Interfaces\IField;
use LiliDb\OrderBy;
use LiliDb\Query\QuerySelect;
use LiliDb\ResultSet;
use PhpFramework\Request\TableRequest;
use PhpFramework\Response\Enum\StatusCode;

class TableResponse extends Response
{
    public int $recordsTotal;

    public int $recordsFiltered;

    public int $draw;

    public ResultSet $data;

    public function __construct(
        private TableRequest $Request,
        private QuerySelect $Query,
        StatusCode $StatusCode = StatusCode::Ok
    ) {
        foreach ($Request->Order as $item) {
            if (isset($Request->Columns[$item['column']]['data'])) {
                $column = $Request->Columns[$item['column']]['data'];
                $dir = $item['dir'];

                foreach ($Query->Select as $Field) {
                    if ($Field instanceof IField && $Field->Name == $column) {
                        if ($dir == 'asc') {
                            $Query = $Query->OrderByValue(OrderBy::Asc($Field));
                        } else {
                            $Query = $Query->OrderByValue(OrderBy::Desc($Field));
                        }
                    }
                }
            }
        }

        $Result = $Query->ExecutePage($Request->Start, $Request->Length);

        if ($Result->Error) {
            throw $Result->Error;
        }

        $this->draw = $Request?->Draw ?? 1;
        $this->recordsTotal = $Result->Total;
        $this->recordsFiltered = $Result->Total;

        $this->data = $Result;

        parent::__construct(null, $StatusCode);
    }
}
