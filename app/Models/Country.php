<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['areaId', 'regionId', 'capital', 'code', 'slug'];

    protected $with = ['dependencies', 'state'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function dependencies()
    {
        return $this->hasMany(Dependency::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function state()
    {
        return $this->morphTo();
    }
}
