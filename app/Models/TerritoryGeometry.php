<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerritoryGeometry extends Model
{
    use HasFactory;

    public function territory()
    {
        return $this->morphTo();
    }
}
