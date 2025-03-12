<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('next Monday', '+10 days');
        $duration = $this->faker->randomElement([15, 30, 60]);
        $endTime = Carbon::instance($startTime)->addMinutes($duration);

        return [
            'user_id' => null,
            'owner_id' => User::factory(),
            'guest_email' => $this->faker->safeEmail,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $this->faker->randomElement(['confirmed', 'canceled', 'past']),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
