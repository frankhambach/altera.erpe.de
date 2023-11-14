<?php

namespace App\Models;

use App\Enums\ScriptCase;
use App\Enums\ScriptFitting;
use App\Enums\ScriptFlow;
use App\Enums\ScriptFormat;
use App\Enums\ScriptHorizontalOrientation;
use App\Enums\ScriptType;
use App\Enums\ScriptVerticalOrientation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Script extends Model
{
    use HasFactory;

    protected $casts = [
        'case' => ScriptCase::class,
        'fitting' => ScriptFitting::class,
        'flow' => ScriptFlow::class,
        'format' => ScriptFormat::class,
        'horizontal_orientation' => ScriptHorizontalOrientation::class,
        'type' => ScriptType::class,
        'vertical_orientation' => ScriptVerticalOrientation::class,
    ];

    protected $fillable = ['code', 'case', 'fitting', 'flow', 'format', 'horizontalOrientation', 'name', 'slug', 'type', 'verticalOrientation'];

    protected $with = ['source'];

    public function cultures()
    {
        return $this->hasMany(Culture::class);
    }

    public function source()
    {
        return $this->hasOne(ScriptSource::class);
    }
}
