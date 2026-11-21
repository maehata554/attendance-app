<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'attendance_date' => now()->toDateString(),
            'clock_in' => now()->setTime(9, 0),
            'clock_out' => now()->setTime(18, 0),
            'status' => 'finished',
            //
        ];
    }

    public function withBreaks(): static
    {
        return $this->afterCreating(function (Attendance $attendance) {
            $attendance->breakRecords()->create([
                'break_in' => now()->setTime(12, 0),
                'break_out' => now()->setTime(13, 0),
            ]);
        });
    }
}
