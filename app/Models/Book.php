<?php

namespace App\Models;

use App\Enums\BookStatus;
use App\Enums\TimeSlot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\SerializesDatesAsUnixTimestamp;

class Book extends Model
{
    use HasFactory, SoftDeletes, SerializesDatesAsUnixTimestamp;

    // Store model timestamps as Unix seconds in DB
    protected $dateFormat = 'U';

    protected $fillable = [
        'employee_id',
        'date',
        'time_slot',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
    ];

    protected $casts = [
        'date' => 'integer',
        'time_slot' => TimeSlot::class,
        'status' => BookStatus::class,
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
