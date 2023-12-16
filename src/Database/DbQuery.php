<?php

namespace PhpFramework\Database;

use Closure;
use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\Attributes\Table;
use PhpFramework\Database\Enumerations\DbLogic;
use PhpFramework\Database\Helpers\SqlFormatter;

class DbQuery
{
    public function __construct(
        public ?DbLogic $Prefix = null,
        public ?Table $Table = null,
        public array $Query = [],
        public array $Parameters = [],
        public ?int $Limit = null,
        public ?int $Offset = null,
    ) {
    }

    public function __toString(): string
    {
        $Query = $this->ToSql();

        $Parameters = $this->Parameters;
        while (str_contains($Query, '?')) {
            $Value = array_shift($Parameters);
            $Query = substr_replace($Query, "'" . $Value . "'", strpos($Query, '?'), 1);
        }

        return $Query;
    }

    public function ToSql(Closure|string|null $ParameterMarker = null, bool $UpdateSetIgnore = false): string
    {
        $Sql = [];
        foreach ($this->Query as $Item) {
            if ($Item instanceof DbValue) {
                if ($ParameterMarker !== null && $Item->Value !== null && !($Item->Value instanceof Field || $Item->Value instanceof DbValue)) {
                    $Item->ParameterMarker = $ParameterMarker;
                }
                if ($UpdateSetIgnore && $Item->IsUpdateSet && $Item->Field !== null) {
                    $Item->Field->Table = null;
                }
                $Sql[] = $Item->__toString();
            } else {
                $Sql[] = $Item;
            }
        }
        $this->Query = [SqlFormatter::format(implode(' ', $Sql), false)];

        return $this->Query[0];
    }
}
