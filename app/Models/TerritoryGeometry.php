<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\MultiLineString;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class TerritoryGeometry extends Model
{
    use HasFactory;
    use HasSpatial;

    protected $casts = [
        'land' => MultiPolygon::class,
        'sea' => MultiPolygon::class,
        'landBorder' => MultiLineString::class,
        'seaBorder' => MultiLineString::class,
        'coast' => MultiLineString::class,
    ];

    protected $fillable = ['land', 'sea', 'landBorder', 'seaBorder', 'coast'];

    public function territory()
    {
        return $this->morphTo();
    }
}
