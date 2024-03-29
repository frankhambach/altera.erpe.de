<?php

namespace App\Models;

use App\Support\HasDemonym;
use App\Support\HasGeometry;
use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dependency extends Model
{
    use HasDemonym;
    use HasFactory;
    use HasGeometry;
    use HasName;

    protected $fillable = ['areaId', 'countryId', 'regionId', 'slug'];

    protected $with = ['name'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function culture()
    {
        return $this->morphOne(Culture::class, 'state');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
