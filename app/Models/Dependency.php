<?php

namespace App\Models;

use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dependency extends Model
{
    use HasFactory;
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

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
