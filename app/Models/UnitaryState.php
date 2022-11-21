<?php

namespace App\Models;

use App\Support\HasDemonym;
use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitaryState extends Model
{
    use HasFactory;
    use HasName;
    use HasDemonym;

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
