<?php

namespace App\Enums;

enum ScriptType: string
{
    case Abjad = 'abjad';
    case Abugida = 'abugida';
    case Alphabet = 'alphabet';
    case Charactery = 'charactery';
    case Featurary = 'featurary';
    case Ideatary = 'ideatary';
    case None = 'none';
    case Syllabary = 'syllabary';
}
