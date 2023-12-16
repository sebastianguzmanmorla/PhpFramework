<?php

namespace Model\Layout;

use PhpFramework\Database\DbTable;

class PermisoHelper extends DbTable
{
    public function __construct(
        public ?int $Id = null,
        public ?int $IdParent = null,
        public ?string $Icon = null,
        public ?string $Label = null,
        public ?string $Route = null,
        public ?bool $Active = null
    ) {
    }
}
