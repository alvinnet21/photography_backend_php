<?php

namespace App\Models\Concerns;

use DateTimeInterface;

trait SerializesDatesAsUnixTimestamp
{
    protected function serializeDate(DateTimeInterface $date): int
    {
        return $date->getTimestamp();
    }
}

