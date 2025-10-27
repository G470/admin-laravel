<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Rental;
use App\Models\Category;
use App\Models\Location;
use App\Models\RentalPush;
use App\Models\VendorCredit;
use App\Models\CreditPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RentalPushTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $vendor;
    protected $rental;
    protected $category;
    protected $location;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->vendor = User::factory()->create(['is_vendor' => true]);
        $this->category = Category::factory()->create();
        $this->location = Location::factory()->create();
        $this->rental = Rental::factory()->create([
            'vendor_id' => $this->vendor->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'status' => 'active'
        ]);

        // Create welcome credit package
        $this->welcomePackage = CreditPackage::create([
            'name' => 'Willkommens-Credits',
            'credits_amount' => 122,
            'standard_price' => 0.00,
            'offer_price' => 0.00,
            'description' => 'Kostenlose Willkommens-Credits für neue Vendor',
            'sort_order' => 0,
            'is_active' => true
        ]);

        // Give vendor some credits
        VendorCredit::create([
            'vendor_id' => $this->vendor->id,
            'credit_package_id' => $this->welcomePackage->id,
            'credits_purchased' => 122,
            'credits_remaining' => 122,
            'amount_paid' => 0.00,
            'payment_status' => 'completed',
            'payment_reference' => 'WELCOME_' . $this->vendor->id,
            'payment_provider' => 'system',
            'purchased_at' => now()
        ]);
    }

    /** @test */
    public function vendor_can_view_rental_pushes_index()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.rental-pushes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('vendor.rental-pushes.index');
        $response->assertViewHas('rentalPushes');
    }

    /** @test */
    public function vendor_can_view_create_rental_push_form()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.rental-pushes.create'));

        $response->assertStatus(200);
        $response->assertViewIs('vendor.rental-pushes.create');
        $response->assertViewHasAll(['rentals', 'categories', 'locations', 'vendorBalance']);
    }

    /** @test */
    public function vendor_can_create_rental_push()
    {
        $pushData = [
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.rental-pushes.store'), $pushData);

        $response->assertRedirect(route('vendor.rental-pushes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rental_pushes', [
            'vendor_id' => $this->vendor->id,
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function vendor_cannot_create_push_without_sufficient_credits()
    {
        // Remove all credits
        VendorCredit::where('vendor_id', $this->vendor->id)->delete();

        $pushData = [
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->vendor)
            ->post(route('vendor.rental-pushes.store'), $pushData);

        $response->assertSessionHasErrors(['credits']);
        $this->assertDatabaseMissing('rental_pushes', [
            'vendor_id' => $this->vendor->id,
            'rental_id' => $this->rental->id,
        ]);
    }

    /** @test */
    public function vendor_can_view_rental_push_details()
    {
        $rentalPush = RentalPush::create([
            'vendor_id' => $this->vendor->id,
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'credits_per_push' => 1,
            'total_credits_needed' => 49,
            'credits_used' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'next_push_at' => now(),
            'is_active' => true
        ]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.rental-pushes.show', $rentalPush));

        $response->assertStatus(200);
        $response->assertViewIs('vendor.rental-pushes.show');
        $response->assertViewHas('rentalPush');
    }

    /** @test */
    public function vendor_can_edit_rental_push()
    {
        $rentalPush = RentalPush::create([
            'vendor_id' => $this->vendor->id,
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'credits_per_push' => 1,
            'total_credits_needed' => 49,
            'credits_used' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'next_push_at' => now(),
            'is_active' => true
        ]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.rental-pushes.edit', $rentalPush));

        $response->assertStatus(200);
        $response->assertViewIs('vendor.rental-pushes.edit');
        $response->assertViewHas('rentalPush');
    }

    /** @test */
    public function vendor_can_update_rental_push()
    {
        $rentalPush = RentalPush::create([
            'vendor_id' => $this->vendor->id,
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'credits_per_push' => 1,
            'total_credits_needed' => 49,
            'credits_used' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'next_push_at' => now(),
            'is_active' => true
        ]);

        $updateData = [
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 5,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'paused'
        ];

        $response = $this->actingAs($this->vendor)
            ->put(route('vendor.rental-pushes.update', $rentalPush), $updateData);

        $response->assertRedirect(route('vendor.rental-pushes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rental_pushes', [
            'id' => $rentalPush->id,
            'frequency' => 5,
            'status' => 'paused'
        ]);
    }

    /** @test */
    public function vendor_can_toggle_rental_push_status()
    {
        $rentalPush = RentalPush::create([
            'vendor_id' => $this->vendor->id,
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'credits_per_push' => 1,
            'total_credits_needed' => 49,
            'credits_used' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'next_push_at' => now(),
            'is_active' => true
        ]);

        $response = $this->actingAs($this->vendor)
            ->patch(route('vendor.rental-pushes.toggle-status', $rentalPush));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rental_pushes', [
            'id' => $rentalPush->id,
            'status' => 'paused'
        ]);
    }

    /** @test */
    public function vendor_can_cancel_rental_push()
    {
        $rentalPush = RentalPush::create([
            'vendor_id' => $this->vendor->id,
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'credits_per_push' => 1,
            'total_credits_needed' => 49,
            'credits_used' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'next_push_at' => now(),
            'is_active' => true
        ]);

        $response = $this->actingAs($this->vendor)
            ->delete(route('vendor.rental-pushes.destroy', $rentalPush));

        $response->assertRedirect(route('vendor.rental-pushes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rental_pushes', [
            'id' => $rentalPush->id,
            'status' => 'cancelled',
            'is_active' => false
        ]);
    }

    /** @test */
    public function vendor_cannot_access_other_vendor_push()
    {
        $otherVendor = User::factory()->create(['is_vendor' => true]);
        $rentalPush = RentalPush::create([
            'vendor_id' => $otherVendor->id,
            'rental_id' => $this->rental->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'frequency' => 7,
            'credits_per_push' => 1,
            'total_credits_needed' => 49,
            'credits_used' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'next_push_at' => now(),
            'is_active' => true
        ]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.rental-pushes.show', $rentalPush));

        $response->assertStatus(403);
    }

    /** @test */
    public function non_vendor_cannot_access_rental_pushes()
    {
        $user = User::factory()->create(['is_vendor' => false]);

        $response = $this->actingAs($user)
            ->get(route('vendor.rental-pushes.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function rental_push_statistics_endpoint_works()
    {
        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.rental-pushes.statistics'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_pushes',
            'active_pushes',
            'total_credits_used',
            'current_balance',
            'pushes_this_month'
        ]);
    }
}
