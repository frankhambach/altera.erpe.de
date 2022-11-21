<?php

namespace App\Support;

use App\Models\TerritoryOfficialName;

trait HasOfficialName
{
    public function officialName()
    {
        return $this->morphOne(TerritoryOfficialName::class, 'territory');
    }
}
