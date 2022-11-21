<?php

namespace App\Models;

use App\Enums\GrammaticalNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TerritoryDemonym extends Model
{
    use HasFactory;

    protected $casts = [
        'grammatical_number' => GrammaticalNumber::class,
    ];

    protected $fillable = ['prefix', 'noun', 'adjective'];

    public function territory()
    {
        return $this->morphTo();
    }
}
