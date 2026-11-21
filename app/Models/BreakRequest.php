<?php

namespace App\Models;
use App\Models\BreakRecord;
use App\Models\AttendanceRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'break_record_id',
        'attendance_request_id',
        'user_id',
        'attendance_date',
        'requested_break_in',
        'requested_break_out',
        'break_in',
        'break_out',
        'status',
        'comment',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'requested_break_in' => 'datetime',
        'requested_break_out' => 'datetime',
        'break_in' => 'datetime',
        'break_out' => 'datetime',
    ];

    public function breakRecord():BelongsTo
    {
        return $this->belongsTo(BreakRecord::class);
    }

    public function attendanceRequest():BelongsTo
    {
        return $this->belongsTo(AttendanceRequest::class);
    }
}
