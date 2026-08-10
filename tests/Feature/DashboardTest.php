<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_dashboard_uses_wib_time_for_shift_details(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-10 04:00:00', 'UTC'));

        try {
            $user = User::factory()->create();
            $this->actingAs($user);

            $response = $this->get('/dashboard');

            $response->assertStatus(200);
            $response->assertSee('Shift Pagi');
            $response->assertSee('07:00 sampai 15:00 WIB');
            $response->assertDontSee('Shift Malam');
        } finally {
            Carbon::setTestNow();
        }
    }
}
