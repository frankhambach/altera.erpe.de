<?php

namespace App\Models;

use App\Enums\GrammaticalNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TerritoryName extends Model
{
    use HasFactory;

    protected $casts = [
        'grammatical_number' => GrammaticalNumber::class,
    ];

    protected $fillable = ['name', 'grammaticalNumber'];

    public function territory()
    {
        return $this->morphTo();
    }
}
