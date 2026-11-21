<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'attendance_date',
        'requested_clock_in',
        'requested_clock_out',
        'status',
        'comment',
    ];

    protected $casts = [
        'attendance_date' => 'datetime',
        'requested_clock_in' => 'datetime',
        'requested_clock_out' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
