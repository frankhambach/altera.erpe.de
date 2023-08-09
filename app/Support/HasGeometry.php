<?php

namespace App\Support;

use App\Models\TerritoryGeometry;

trait HasGeometry
{
    public function geometry()
    {
        return $this->morphOne(TerritoryGeometry::class, 'territory');
    }
}
