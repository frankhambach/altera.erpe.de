<?php

namespace App\Models;

use App\Support\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quarter extends Model
{
    use HasFactory;
    use HasName;

    protected $fillable = ['regionId', 'slug'];

    protected $with = ['name'];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
