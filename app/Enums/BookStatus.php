<?php

namespace App\Enums;

enum BookStatus: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case REJECTED = 'REJECTED';
    case FINISHED = 'FINISHED';
}
