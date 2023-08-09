<?php

namespace App\Models;

use App\Support\HasGeometry;
use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Continent extends Model
{
    use HasFactory;
    use HasGeometry;
    use HasName;

    protected $fillable = ['landmassId', 'slug'];

    protected $with = ['name', 'regions'];

    public function landmass()
    {
        return $this->belongsTo(Landmass::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
