<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cant create a slot as guest user.
     */
    public function test_guest_user_cant_create_availability(): void
    {
        $params = [
            'user_id' => 1, // Guest does not have an id, so we just put a random number
            'day_of_week' => 0, // Monday
            'start_time' => '09:00',
            'end_time' => '12:00',
            'slot_duration' => 60, // 1 hour
        ];

        $response = $this->post(route('availability.store'), $params);

        // Assert redirect to login
        $response->assertRedirect(route('login'));

        // Assert DB values missing
        $this->assertDatabaseMissing('availabilities', $params);
    }

    /**
     * Create a slot as auth user.
     */
    public function test_auth_user_can_create_availability(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $params = [
            'user_id' => $user->id,
            'day_of_week' => 0, // Monday
            'start_time' => '09:00',
            'end_time' => '12:00',
            'slot_duration' => 60, // 1 hour
        ];

        // Call POST route
        $response = $this->post(route('availability.store'), $params);

        // Assert redirect to dashboard + success
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // Assert DB values
        $this->assertDatabaseHas('availabilities', $params);
    }

    /**
     * Can't create a slot if another slot exists on the same time.
     */
    public function test_cant_overlap_an_existing_availability(): void
    {
        $user = User::factory()->create();

        $params = [
            'user_id' => $user->id,
            'day_of_week' => 0,
            'start_time' => '10:00',
            'end_time' => '15:00',
            'slot_duration' => 60,
        ];

        // Create a first entry
        Availability::create($params);

        // User create a new one with the same time slot to overlap
        $this->actingAs($user);
        $response = $this->post(route('availability.store'), $params);
        $response->assertSessionHasErrors('overlap');

        // Be sure that the last one was not created in DB
        $count = Availability::where('user_id', $user->id)
            ->where('day_of_week', $params['day_of_week'])
            ->where('start_time', $params['start_time'])
            ->where('end_time', $params['end_time'])
            ->where('slot_duration', $params['slot_duration'])
            ->count();

        $this->assertEquals(1, $count);
    }
}
