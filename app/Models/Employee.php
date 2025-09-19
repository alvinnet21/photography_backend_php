<?php

namespace App\Models;

use App\Enums\EmployeePosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\SerializesDatesAsUnixTimestamp;

class Employee extends Model
{
    use HasFactory, SoftDeletes, SerializesDatesAsUnixTimestamp;

    // Store dates in DB as Unix timestamps
    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position'
    ];

    protected $casts = [
        'position' => EmployeePosition::class,
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
