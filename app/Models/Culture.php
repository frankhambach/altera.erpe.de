<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Culture extends Model
{
    use HasFactory;

    protected $fillable = ['slug'];

    public function script()
    {
        return $this->belongsTo(Script::class);
    }

    public function state()
    {
        return $this->morphTo();
    }
}
