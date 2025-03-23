<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
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
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'slot_duration' => 60,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
