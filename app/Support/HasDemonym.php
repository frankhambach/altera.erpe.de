<?php

namespace App\Support;

use App\Models\TerritoryDemonym;

trait HasDemonym
{
    public function demonym()
    {
        return $this->morphOne(TerritoryDemonym::class, 'territory');
    }
}
