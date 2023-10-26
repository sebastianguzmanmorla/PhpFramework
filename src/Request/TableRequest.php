<?php

namespace PhpFramework\Request;

class TableRequest
{
    public array $Columns = [];

    public array $Order = [];

    public int $Draw = 0;

    public int $Start = 0;

    public int $Length = 10;

    public function __construct()
    {
        $this->Columns = $_POST['columns'] ?? [];
        $this->Order = $_POST['order'] ?? [];
        $this->Draw = isset($_POST['draw']) ? (int) ($_POST['draw']) : 0;
        $this->Start = isset($_POST['start']) ? (int) ($_POST['start']) : 0;
        $this->Length = isset($_POST['length']) ? (int) ($_POST['length']) : 10;
    }
}
