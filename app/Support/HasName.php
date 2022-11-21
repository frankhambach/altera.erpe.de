<?php

namespace App\Support;

use App\Models\TerritoryName;

trait HasName
{
    public function name()
    {
        return $this->morphOne(TerritoryName::class, 'territory');
    }
}
