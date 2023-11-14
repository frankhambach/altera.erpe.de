<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScriptSource extends Model
{
    use HasFactory;

    protected $fillable = ['isoCode', 'name', 'notes', 'omniglotCode', 'scriptId', 'wikidataId'];

    public function script()
    {
        return $this->hasOne(Script::class);
    }
}
