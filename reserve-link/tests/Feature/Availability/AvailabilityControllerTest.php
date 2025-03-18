<?php

namespace Tests\Feature;

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
        'slot_duration' => 60 // 1 hour
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
        'slot_duration' => 60 // 1 hour
      ];

      // Call POST route
      $response = $this->post(route('availability.store'), $params);

      // Assert redirect to dashboard + success
      $response->assertRedirect(route('dashboard'));
      $response->assertSessionHas('success');

      // Assert DB values
      $this->assertDatabaseHas('availabilities', $params);
    }
}
