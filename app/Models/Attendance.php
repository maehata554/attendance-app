<?php

namespace App\Models;
use App\Models\AttendanceRequest;
use App\Models\BreakRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'clock_in',
        'clock_out',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    //一つの勤怠は複数の勤怠修正申請を持つ
    public function attendanceRequests():HasMany
    {
        return $this->hasMany(AttendanceRequest::class);
    }
    //一つの勤怠は複数の休憩記録を持つ
    public function breakRecord():HasMany
    {
        return $this->hasMany(BreakRecord::class);
    }
}
