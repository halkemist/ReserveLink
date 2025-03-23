<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilitySlotControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create an availability and display it in the calendar.
     */
    public function test_user_availabilities_are_displayed_correctly(): void
    {
        $user = User::factory()->create();

        $params = [
            'user_id' => $user->id,
            'day_of_week' => 0, // Monday
            'start_time' => '09:00',
            'end_time' => '10:00',
            'slot_duration' => 60,
        ];

        Availability::create($params);

        $response = $this->get(route('calendar', $user->email));

        $response->assertStatus(200);
        $response->assertViewHas('slots');
        $response->assertViewHas('user');

        // Check if response contains slots and user arrays
        $this->assertArrayHasKey('slots', $response);
        $this->assertArrayHasKey('user', $response);

        // Compare our params with returned availability value (must be the same)
        $this->assertStringContainsString($params['start_time'], $response['slots'][0]['start_time']);
        $this->assertStringContainsString($params['end_time'], $response['slots'][0]['end_time']);

        // Compare our user with the returned user (must be the same)
        $this->assertEquals($user->id, $response['user']['id']);
    }

    /**
     * Create an availability, book it, and be sure that isn't present anymore in the calendar.
     */
    public function test_availability_must_not_be_displayed(): void
    {
        $user = User::factory()->create();

        $availabilityParams = [
            'user_id' => $user->id,
            'day_of_week' => 0, // Monday
            'start_time' => '09:00',
            'end_time' => '10:00',
            'slot_duration' => 60,
        ];

        Availability::create($availabilityParams);

        // Get full time slots
        $response = $this->get(route('calendar', $user->email));

        // Prepare params to book the first time slot
        $bookingParams = [
            'owner_id' => $user->id,
            'guest_email' => 'test@gmail.com',
            'start_time' => $response['slots'][0]['start_time'],
            'end_time' => $response['slots'][0]['end_time'],
            'status' => 'confirmed',
        ];

        // Book the availability slot
        $bookFirst = $this->post(route('booking.store', $bookingParams));
        $bookFirst->assertSessionHas('success');

        // Try to book the same slot a new time
        $bookSecond = $this->post(route('booking.store', $bookingParams));
        $bookSecond->assertStatus(403);
    }
}
