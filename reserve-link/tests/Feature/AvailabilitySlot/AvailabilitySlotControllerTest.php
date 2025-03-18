<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilitySlotControllerTest extends TestCase
{
  use RefreshDatabase;

  /**
   * A basic test example.
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
}