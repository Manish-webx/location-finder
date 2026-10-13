<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\LocationLink;
use App\Models\LocationVisit;
use Carbon\Carbon;

class LocationTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_admin_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('GeoTrack');
    }

    public function test_can_create_new_location_link(): void
    {
        $response = $this->post('/admin/links', [
            'title' => 'Field Test #1',
            'expires_in_hours' => 24,
        ]);

        $response->assertRedirect(route('dashboard.index'));
        $this->assertDatabaseHas('location_links', [
            'title' => 'Field Test #1',
            'is_active' => true,
        ]);
    }

    public function test_can_access_consent_page_with_valid_token(): void
    {
        $link = LocationLink::create([
            'token' => 'TEST01',
            'title' => 'Consent Test Session',
            'is_active' => true,
            'expires_at' => Carbon::now()->addDay(),
        ]);

        $response = $this->get("/l/{$link->token}");
        $response->assertStatus(200);
        $response->assertSee('Consent Test Session');
        $response->assertSee('Click & Earn Now', false);
    }

    public function test_cannot_access_expired_or_disabled_link(): void
    {
        $expiredLink = LocationLink::create([
            'token' => 'EXP999',
            'title' => 'Expired Session',
            'is_active' => true,
            'expires_at' => Carbon::now()->subHour(),
        ]);

        $response = $this->get("/l/{$expiredLink->token}");
        $response->assertStatus(200);
        $response->assertSee('Link Inactive or Not Found');
    }

    public function test_can_submit_gps_coordinates_with_valid_token(): void
    {
        $link = LocationLink::create([
            'token' => 'LOC123',
            'title' => 'Live Tracker Test',
            'is_active' => true,
        ]);

        $payload = [
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'accuracy' => 12.5,
            'altitude' => 216.0,
            'speed' => 1.4,
            'device_info' => 'Chrome on Windows 11',
        ];

        $response = $this->postJson("/l/{$link->token}/share", $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('location_visits', [
            'location_link_id' => $link->id,
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'accuracy' => 12.5,
        ]);
    }

    public function test_can_fetch_live_api_data_for_dashboard(): void
    {
        $link = LocationLink::create([
            'token' => 'MAP456',
            'title' => 'Map Test',
            'is_active' => true,
        ]);

        LocationVisit::create([
            'location_link_id' => $link->id,
            'latitude' => 19.0760,
            'longitude' => 72.8777,
            'accuracy' => 8.0,
        ]);

        $response = $this->getJson("/admin/api/live-data/{$link->id}");
        $response->assertStatus(200);
        $response->assertJsonPath('visits.0.latitude', 19.076);
    }
}
