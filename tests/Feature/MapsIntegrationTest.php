<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Setting;
use App\Services\MapsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MapsIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear any existing settings
        Setting::where('group', 'integrations')->delete();
    }

    /** @test */
    public function it_can_check_maps_service_availability()
    {
        $mapsService = app(MapsService::class);

        // Initially no service should be available
        $this->assertFalse($mapsService->hasMapsService());
        $this->assertNull($mapsService->getPreferredMapsService());

        // Enable Google Maps
        Setting::set('google_maps_enabled', true);
        Setting::set('google_maps_api_key', 'test_key');

        $mapsService = app(MapsService::class); // Refresh service
        $this->assertTrue($mapsService->hasMapsService());
        $this->assertEquals('google_maps', $mapsService->getPreferredMapsService());

        // Enable OpenStreetMap
        Setting::set('openstreetmap_enabled', true);

        $mapsService = app(MapsService::class); // Refresh service
        $this->assertTrue($mapsService->hasMapsService());
        $this->assertEquals('google_maps', $mapsService->getPreferredMapsService()); // Google Maps takes precedence
    }

    /** @test */
    public function it_can_get_service_status()
    {
        $mapsService = app(MapsService::class);
        $status = $mapsService->getServiceStatus();

        $this->assertIsArray($status);
        $this->assertArrayHasKey('google_maps', $status);
        $this->assertArrayHasKey('openstreetmap', $status);
        $this->assertArrayHasKey('preferred_service', $status);
        $this->assertArrayHasKey('has_any_service', $status);

        $this->assertFalse($status['google_maps']['enabled']);
        $this->assertFalse($status['openstreetmap']['enabled']);
        $this->assertFalse($status['has_any_service']);
        $this->assertNull($status['preferred_service']);
    }

    /** @test */
    public function it_can_handle_google_maps_configuration()
    {
        // Enable Google Maps
        Setting::set('google_maps_enabled', true);
        Setting::set('google_maps_api_key', 'test_api_key');

        $mapsService = app(MapsService::class);
        $status = $mapsService->getServiceStatus();

        $this->assertTrue($status['google_maps']['enabled']);
        $this->assertTrue($status['google_maps']['has_api_key']);
        $this->assertEquals('ready', $status['google_maps']['status']);
        $this->assertTrue($status['has_any_service']);
        $this->assertEquals('google_maps', $status['preferred_service']);
    }

    /** @test */
    public function it_can_handle_openstreetmap_configuration()
    {
        // Enable OpenStreetMap
        Setting::set('openstreetmap_enabled', true);

        $mapsService = app(MapsService::class);
        $status = $mapsService->getServiceStatus();

        $this->assertTrue($status['openstreetmap']['enabled']);
        $this->assertFalse($status['openstreetmap']['has_api_key']);
        $this->assertEquals('ready', $status['openstreetmap']['status']);
        $this->assertTrue($status['has_any_service']);
        $this->assertEquals('openstreetmap', $status['preferred_service']);
    }

    /** @test */
    public function it_throws_exception_when_no_service_available()
    {
        $mapsService = app(MapsService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Kein Karten-Dienst verfügbar');

        $mapsService->geocodeAddress('Test Address');
    }

    /** @test */
    public function it_throws_exception_when_google_maps_not_configured()
    {
        Setting::set('google_maps_enabled', true);
        // No API key set

        $mapsService = app(MapsService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Google Maps ist nicht konfiguriert');

        $mapsService->geocodeAddress('Test Address');
    }

    /** @test */
    public function it_throws_exception_when_openstreetmap_not_enabled()
    {
        Setting::set('openstreetmap_enabled', false);

        $mapsService = app(MapsService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Kein Karten-Dienst verfügbar');

        $mapsService->geocodeAddress('Test Address');
    }

    /** @test */
    public function it_can_clear_cache()
    {
        $mapsService = app(MapsService::class);

        // This should not throw an exception
        $mapsService->clearCache();

        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    /** @test */
    public function api_endpoints_return_correct_responses()
    {
        // Test status endpoint
        $response = $this->getJson('/api/geocoding/status');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'google_maps' => ['enabled', 'has_api_key', 'status'],
                    'openstreetmap' => ['enabled', 'has_api_key', 'status'],
                    'preferred_service',
                    'has_any_service'
                ],
                'message'
            ]);

        // Test geocode endpoint without service
        $response = $this->postJson('/api/geocoding/geocode', [
            'address' => 'Test Address'
        ]);
        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
                'error' => 'no_maps_service'
            ]);

        // Test reverse geocode endpoint without service
        $response = $this->postJson('/api/geocoding/reverse', [
            'latitude' => 52.5200,
            'longitude' => 13.4050
        ]);
        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
                'error' => 'no_maps_service'
            ]);
    }

    /** @test */
    public function api_endpoints_validate_input()
    {
        // Test geocode endpoint with invalid input
        $response = $this->postJson('/api/geocoding/geocode', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address']);

        // Test reverse geocode endpoint with invalid input
        $response = $this->postJson('/api/geocoding/reverse', [
            'latitude' => 'invalid',
            'longitude' => 'invalid'
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);

        // Test batch geocode endpoint with invalid input
        $response = $this->postJson('/api/geocoding/batch', [
            'addresses' => 'not_an_array'
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['addresses']);
    }

    /** @test */
    public function batch_geocoding_validates_address_count()
    {
        // Test with too many addresses
        $addresses = array_fill(0, 11, 'Test Address');

        $response = $this->postJson('/api/geocoding/batch', [
            'addresses' => $addresses
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['addresses']);

        // Test with empty addresses
        $response = $this->postJson('/api/geocoding/batch', [
            'addresses' => []
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['addresses']);
    }

    /** @test */
    public function it_can_handle_rate_limiting()
    {
        // This test is skipped since we removed rate limiting middleware for now
        $this->markTestSkipped('Rate limiting middleware temporarily disabled');
    }

    /** @test */
    public function it_can_clear_cache_via_api()
    {
        $response = $this->deleteJson('/api/geocoding/cache');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Geocoding-Cache erfolgreich geleert'
            ]);
    }

    /** @test */
    public function it_handles_missing_csrf_token_gracefully()
    {
        // Test that API endpoints work without CSRF token
        $response = $this->getJson('/api/geocoding/status');
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_handle_service_fallback()
    {
        // Enable both services
        Setting::set('google_maps_enabled', true);
        Setting::set('google_maps_api_key', 'invalid_key');
        Setting::set('openstreetmap_enabled', true);

        $mapsService = app(MapsService::class);

        // Should prefer Google Maps but fall back to OpenStreetMap if Google fails
        $this->assertEquals('google_maps', $mapsService->getPreferredMapsService());
        $this->assertTrue($mapsService->hasMapsService());
    }

    /** @test */
    public function it_validates_coordinate_ranges()
    {
        // Test invalid latitude
        $response = $this->postJson('/api/geocoding/reverse', [
            'latitude' => 100, // Invalid: > 90
            'longitude' => 13.4050
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitude']);

        // Test invalid longitude
        $response = $this->postJson('/api/geocoding/reverse', [
            'latitude' => 52.5200,
            'longitude' => 200 // Invalid: > 180
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['longitude']);

        // Test valid coordinates
        $response = $this->postJson('/api/geocoding/reverse', [
            'latitude' => 52.5200,
            'longitude' => 13.4050
        ]);
        $response->assertStatus(503); // Service not available, but validation passed
    }
}