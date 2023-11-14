<?php

namespace App\Enums;

enum ScriptFlow: string
{
    case Linear = 'linear';
    case None = 'none';
    case ZigZag = 'zig_zag';
}
