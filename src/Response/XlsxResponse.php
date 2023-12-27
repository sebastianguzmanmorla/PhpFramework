<?php

namespace PhpFramework\Response;

use PhpFramework\Attributes\Singleton;
use PhpFramework\Html\Markup;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Interface\IResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ReflectionClass;
use Throwable;

class XlsxResponse implements IResponse
{
    public Spreadsheet $Spreadsheet;

    public function __construct(
        public StatusCode $StatusCode = StatusCode::Ok,
        public string $Path = '',
        public string $Name = '',
        public ?string $FullPath = null
    ) {
        $this->FullPath ??= $this->Path . $this->Name;

        $ReflectionClass = new ReflectionClass(static::class);

        foreach ($ReflectionClass->getProperties() as $Property) {
            $Singleton = $Property->getAttributes(Singleton::class);
            if (!empty($Singleton)) {
                $PropertyType = $Property->getType();
                $PropertyValue = Singleton::Get($PropertyType->getName());
                $Property->setValue($this, $PropertyValue);
            }

            $Attributes = $Property->getAttributes();
            foreach ($Attributes as $Attribute) {
                $Value = $Attribute->newInstance();
                if ($Value instanceof Markup) {
                    $Property->setValue($this, $Value);
                }
            }
        }

        $this->Spreadsheet = new Spreadsheet();
    }

    final public function Open(): bool
    {
        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $this->Spreadsheet = $reader->load($this->FullPath);

            return true;
        } catch (Throwable $Throwable) {
            throw $Throwable;

            return false;
        }
    }

    final public function Save(): bool
    {
        @mkdir($this->Path, 0o777, true);

        try {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->Spreadsheet);
            $writer->save($this->FullPath);

            return true;
        } catch (Throwable $Throwable) {
            throw $Throwable;

            return false;
        }
    }

    final public function Response(): ?string
    {
        http_response_code($this->StatusCode->value);

        if ($this->Save()) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            header('Content-Disposition: inline; filename=' . $this->Name);

            readfile($this->FullPath);
        }

        return null;
    }

    final public function Body(): void
    {
    }
}
