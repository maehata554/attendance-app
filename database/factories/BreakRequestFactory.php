<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BreakRequest>
 */
class BreakRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requested_break_in' => now()->setTime(13, 0),
            'requested_break_out' => now()->setTime(14, 0),
            'break_in' => now()->setTime(12, 0),
            'break_out' => now()->setTime(13, 0),
        ];
    }
}
