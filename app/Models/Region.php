<?php

namespace App\Models;

use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;
    use HasName;

    protected $fillable = ['continentId', 'slug'];

    protected $with = ['name', 'quarters'];

    public function continent()
    {
        return $this->belongsTo(Continent::class);
    }

    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    public function dependencies()
    {
        return $this->hasMany(Dependency::class);
    }

    public function quarters()
    {
        return $this->hasMany(Quarter::class);
    }
}
