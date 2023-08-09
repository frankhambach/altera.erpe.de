<?php

namespace App\Models;

use App\Support\HasDemonym;
use App\Support\HasName;
use App\Support\HasOfficialName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FederalState extends Model
{
    use HasDemonym;
    use HasFactory;
    use HasName;
    use HasOfficialName;

    protected $fillable = ['slug'];

    protected $with = ['demonym', 'federatedStates', 'name', 'officialName'];

    public function country()
    {
        return $this->morphOne(Country::class, 'state');
    }

    public function federatedStates()
    {
        return $this->hasMany(FederatedState::class);
    }
}
