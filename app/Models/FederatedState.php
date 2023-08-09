<?php

namespace App\Models;

use App\Support\HasGeometry;
use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FederatedState extends Model
{
    use HasFactory;
    use HasGeometry;
    use HasName;

    protected $fillable = ['federalStateId', 'code', 'slug'];

    protected $with = ['culture', 'name'];

    public function culture()
    {
        return $this->morphOne(Culture::class, 'state');
    }

    public function federalState()
    {
        return $this->belongsTo(FederalState::class);
    }
}
