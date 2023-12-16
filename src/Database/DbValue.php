<?php

namespace PhpFramework\Database;

use Closure;
use DateTime;
use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\Enumerations\DbLogic;
use PhpFramework\Database\Enumerations\DbOrder;
use PhpFramework\Database\Enumerations\DbWhere;

class DbValue
{
    public function __construct(
        public ?Field $Field = null,
        public ?DbWhere $Where = null,
        public ?DbOrder $Order = null,
        public mixed $Expression = null,
        public mixed $Value = null,
        public mixed $Variable = null,
        public Closure|string $ParameterMarker = '?',
        public bool $IsUpdateSet = false
    ) {
    }

    public function __toString()
    {
        $Return = [];

        if ($this->Value !== null && $this->Value instanceof DateTime) {
            $this->Value = $this->Value->format('Y-m-d H:i:s');
        }

        if ($this->Where !== null) {
            switch ($this->Where) {
                case DbWhere::Between:
                    $Return = [
                        $this->Field?->__toString(),
                        $this->Where->value,
                        $this->GetParameterMarker(),
                        DbLogic::And->value,
                        $this->GetParameterMarker(),
                    ];

                    break;
                case DbWhere::In:
                    $Return = [
                        $this->Field?->__toString(),
                        $this->Where->value,
                        '(' . implode(', ', array_map(fn ($x) => $this->GetParameterMarker(), array_keys($this->Value))) . ')',
                    ];

                    break;
                case DbWhere::NotIn:
                    $Return = [
                        $this->Field?->__toString(),
                        $this->Where->value,
                        '(' . implode(', ', array_map(fn ($x) => $this->GetParameterMarker(), array_keys($this->Value))) . ')',
                    ];

                    break;
                case DbWhere::IsNull:
                    $Return = [
                        $this->Field?->__toString(),
                        $this->Where->value,
                    ];

                    break;
                case DbWhere::IsNotNull:
                    $Return = [
                        $this->Field?->__toString(),
                        $this->Where->value,
                    ];

                    break;
                default:
                    $Value = null;
                    if ($this->Value instanceof Field) {
                        $Value = $this->Value->__toString();
                    } elseif ($this->Value !== null) {
                        $Value = $this->GetParameterMarker();
                    }
                    $Return = [
                        $this->Field?->__toString(),
                        $this->Where->value,
                        $Value,
                    ];

                    break;
            }
        } elseif ($this->Order !== null) {
            $Return = [
                $this->Field?->__toString(),
                $this->Order->value,
            ];
        } elseif ($this->Value !== null && $this->Value instanceof Field) {
            $Return = [
                $this->Value->__toString(),
            ];
        } else {
            $Return = [
                $this->GetParameterMarker(),
            ];
        }

        return implode(' ', $Return);
    }

    public function GetParameterMarker(): string
    {
        if ($this->ParameterMarker instanceof Closure) {
            return $this->ParameterMarker->__invoke();
        }

        return $this->ParameterMarker;
    }
}
