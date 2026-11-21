<?php

namespace Database\Factories;
use App\Models\AttendanceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;



class AttendanceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = AttendanceRequest::class;

    public function definition(): array
    {
        return [
            'attendance_date' => today()->subDay()->toDateString(),
            'requested_clock_in' => now()->setTime(10, 0),
            'requested_clock_out' => now()->setTime(19, 0),
            'clock_in' => now()->setTime(9, 0),
            'clock_out' => now()->setTime(18, 0),
            'comment' => '遅延のため',
            'status' => 'pending',
        ];
    }
}
