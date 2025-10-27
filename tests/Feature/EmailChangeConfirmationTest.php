<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EmailChangeToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailChangeConfirmation;

class EmailChangeConfirmationTest extends TestCase
{
    use RefreshDatabase;

    protected $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a vendor user
        $this->vendor = User::factory()->create([
            'email' => 'vendor@example.com',
            'is_vendor' => true,
        ]);
    }

    /** @test */
    public function vendor_can_request_email_change()
    {
        Notification::fake();

        $response = $this->actingAs($this->vendor)
            ->put('/vendor/email', [
                'new_email' => 'newemail@example.com',
                'new_email_confirmation' => 'newemail@example.com',
            ]);

        $response->assertRedirect('/vendor/personal-data');
        $response->assertSessionHas('success');

        // Check that token was created
        $this->assertDatabaseHas('email_change_tokens', [
            'user_id' => $this->vendor->id,
            'new_email' => 'newemail@example.com',
            'used' => false,
        ]);

        // Check that notification was sent
        Notification::assertSentTo(
            ['newemail@example.com'],
            EmailChangeConfirmation::class
        );
    }

    /** @test */
    public function vendor_cannot_change_to_same_email()
    {
        $response = $this->actingAs($this->vendor)
            ->put('/vendor/email', [
                'new_email' => 'vendor@example.com',
                'new_email_confirmation' => 'vendor@example.com',
            ]);

        $response->assertRedirect('/vendor/personal-data');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function vendor_can_confirm_email_change()
    {
        // Create a token
        $token = EmailChangeToken::createToken($this->vendor->id, 'newemail@example.com');

        $response = $this->actingAs($this->vendor)
            ->get("/vendor/email/confirm/{$token->token}");

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        // Check that email was updated
        $this->vendor->refresh();
        $this->assertEquals('newemail@example.com', $this->vendor->email);
        $this->assertNotNull($this->vendor->email_verified_at);

        // Check that token was marked as used
        $this->assertDatabaseHas('email_change_tokens', [
            'id' => $token->id,
            'used' => true,
        ]);
    }

    /** @test */
    public function vendor_can_cancel_email_change()
    {
        // Create a token
        $token = EmailChangeToken::createToken($this->vendor->id, 'newemail@example.com');

        $response = $this->actingAs($this->vendor)
            ->get("/vendor/email/cancel/{$token->token}");

        $response->assertRedirect('/vendor/personal-data');
        $response->assertSessionHas('success');

        // Check that email was NOT updated
        $this->vendor->refresh();
        $this->assertEquals('vendor@example.com', $this->vendor->email);

        // Check that token was marked as used
        $this->assertDatabaseHas('email_change_tokens', [
            'id' => $token->id,
            'used' => true,
        ]);
    }

    /** @test */
    public function expired_token_is_rejected()
    {
        // Create an expired token
        $token = EmailChangeToken::create([
            'user_id' => $this->vendor->id,
            'new_email' => 'newemail@example.com',
            'token' => 'expired-token',
            'expires_at' => now()->subHour(),
            'used' => false,
        ]);

        $response = $this->actingAs($this->vendor)
            ->get("/vendor/email/confirm/{$token->token}");

        $response->assertRedirect('/vendor/personal-data');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function used_token_is_rejected()
    {
        // Create a used token
        $token = EmailChangeToken::create([
            'user_id' => $this->vendor->id,
            'new_email' => 'newemail@example.com',
            'token' => 'used-token',
            'expires_at' => now()->addHour(),
            'used' => true,
        ]);

        $response = $this->actingAs($this->vendor)
            ->get("/vendor/email/confirm/{$token->token}");

        $response->assertRedirect('/vendor/personal-data');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function invalid_token_is_rejected()
    {
        $response = $this->actingAs($this->vendor)
            ->get('/vendor/email/confirm/invalid-token');

        $response->assertRedirect('/vendor/personal-data');
        $response->assertSessionHas('error');
    }
}