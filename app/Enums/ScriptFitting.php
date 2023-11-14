<?php

namespace App\Enums;

enum ScriptFitting: string
{
    case Continual = 'continual';
    case Interpunctual = 'interpunctual';
    case Interspatial = 'interspatial';
    case None = 'none';
}
