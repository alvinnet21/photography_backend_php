<?php

namespace App\Enums;

enum TimeSlot: string
{
    case MORNING = 'MORNING';
    case AFTERNOON = 'AFTERNOON';
    case FULL_DAY = 'FULL_DAY';
}
