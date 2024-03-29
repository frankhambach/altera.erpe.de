<?php

namespace App\Models;

use App\Support\HasGeometry;
use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Landmass extends Model
{
    use HasFactory;
    use HasGeometry;
    use HasName;

    protected $fillable = ['slug'];

    protected $with = ['name', 'continents'];

    public function continents()
    {
        return $this->hasMany(Continent::class);
    }
}
