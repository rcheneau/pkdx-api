<?php

declare(strict_types=1);

namespace App\Enum;

enum PokemonGrowthRateEnum: string
{
    case Fluctuating = 'fluctuating';
    case Slow = 'slow';
    case MediumSlow = 'medium_slow';
    case MediumFast = 'medium_fast';
    case Fast = 'fast';
    case Erratic = 'erratic';
}
