<?php

namespace App\Models;

use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Area extends Model
{
    use HasFactory;
    use HasName;

    protected $fillable = ['quarterId', 'slug'];

    protected $with = ['name', 'countries', 'dependencies'];

    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    public function dependencies()
    {
        return $this->hasMany(Dependency::class);
    }

    public function quarter()
    {
        return $this->belongsTo(Quarter::class);
    }
}
