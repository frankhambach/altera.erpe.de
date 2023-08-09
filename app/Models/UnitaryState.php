<?php

namespace App\Models;

use App\Support\HasDemonym;
use App\Support\HasGeometry;
use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitaryState extends Model
{
    use HasDemonym;
    use HasFactory;
    use HasGeometry;
    use HasName;

    protected $fillable = ['slug'];

    protected $with = ['culture', 'demonym', 'name'];

    public function country()
    {
        return $this->morphOne(Country::class, 'state');
    }

    public function culture()
    {
        return $this->morphOne(Culture::class, 'state');
    }
}
