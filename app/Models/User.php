<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Role;
use App\Models\Rental;
use App\Models\Location;
use App\Models\Booking;
use App\Models\VendorContactDetail;
use App\Models\VendorCredit;
use App\Models\SubscriptionPlan;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, HasRoles;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'is_admin',
    'is_vendor',
    'salutation',
    'first_name',
    'last_name',
    'street',
    'house_number',
    'address_addition',
    'company_name',
    'company_description',
    'company_logo',
    'company_banner',
    'profile_image',
    'phone',
    'mobile',
    'address',
    'city',
    'postal_code',
    'country',
    'billing_street',
    'billing_house_number',
    'billing_address_addition',
    'billing_city',
    'billing_postal_code',
    'billing_country',
    'notification_email',
    'notification_options',
    'contact_details',
    'use_custom_contact_details',
    'subscription_plan_id',
    'email_verified_at',
    'opening_hours',
    'additional_information',
    'two_factor_enabled',
    'two_factor_confirmed_at',
    'two_factor_enabled_at',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_secret',
    'two_factor_recovery_codes',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'is_admin' => 'boolean',
    'is_vendor' => 'boolean',
    'notification_options' => 'array',
    'contact_details' => 'array',
    'use_custom_contact_details' => 'boolean',
    'opening_hours' => 'array',
    'two_factor_enabled' => 'boolean',
    'two_factor_confirmed_at' => 'datetime',
    'two_factor_enabled_at' => 'datetime',
    'two_factor_recovery_codes' => 'array',
  ];

  // Relationships
  public function subscriptionPlan()
  {
    return $this->belongsTo(SubscriptionPlan::class);
  }

  public function rentals()
  {
    return $this->hasMany(Rental::class, 'vendor_id');
  }

  public function locations(): HasMany
  {
    return $this->hasMany(Location::class, 'vendor_id');
  }

  public function bookings()
  {
    return $this->hasMany(Booking::class);
  }

  public function vendorContactDetails()
  {
    return $this->hasMany(VendorContactDetail::class, 'vendor_id');
  }

  public function defaultContactDetails()
  {
    return $this->hasOne(VendorContactDetail::class, 'vendor_id');
  }

  public function vendorCredits()
  {
    return $this->hasMany(VendorCredit::class, 'vendor_id');
  }

  public function vendorBookings()
  {
    return $this->hasManyThrough(Booking::class, Rental::class, 'vendor_id', 'rental_id');
  }

  public function vendorSubscriptions()
  {
    return $this->hasMany(VendorSubscription::class, 'vendor_id');
  }

  public function currentVendorSubscription()
  {
    return $this->hasOne(VendorSubscription::class, 'vendor_id')->where('status', 'active')->latest();
  }

  // Other relationships can be added here as needed

  // Scopes
  public function scopeVendors($query)
  {
    return $query->where('is_vendor', true);
  }

  public function scopeAdmins($query)
  {
    return $query->where('is_admin', true);
  }

  public function scopeRegularUsers($query)
  {
    return $query->where('is_vendor', false)->where('is_admin', false);
  }

  // Accessors
  public function getFullNameAttribute()
  {
    return $this->first_name . ' ' . $this->last_name;
  }

  public function getFullAddressAttribute()
  {
    return $this->street . ' ' . $this->house_number . ', ' . $this->postal_code . ' ' . $this->city;
  }

  public function getBillingFullAddressAttribute()
  {
    return $this->billing_street . ' ' . $this->billing_house_number . ', ' . $this->billing_postal_code . ' ' . $this->billing_city;
  }

  // Check if user is an admin
  public function isAdmin()
  {
    return $this->is_admin;
  }

  // Check if user is a vendor
  public function isVendor()
  {
    return $this->is_vendor;
  }

  /**
   * Get the notification email for a specific location.
   * Returns custom email if set, otherwise user's main email.
   */
  public function getNotificationEmailForLocation($locationId)
  {
    $notification = $this->notifications()->where('location_id', $locationId)->first();

    if ($notification && $notification->use_custom_notifications && $notification->notification_email) {
      return $notification->notification_email;
    }

    return $this->notification_email ?: $this->email;
  }

  /**
   * Get vendor locations with opening hours status.
   * Returns a collection with location data and opening hours status.
   */
  public function getLocationsWithOpeningHoursAttribute()
  {
    return $this->locations()->get()->map(function ($location) {
      $hasCustomHours = !empty($location->opening_hours);

      return [
        'id' => $location->id,
        'name' => $location->name,
        'city' => $location->city,
        'postal_code' => $location->postal_code,
        'country' => $location->country,
        'street_address' => $location->street_address,
        'additional_address' => $location->additional_address,
        'has_custom_hours' => $hasCustomHours,
        'status' => $hasCustomHours ? 'Eigene' : 'Standard',
        'opening_hours' => $location->opening_hours,
      ];
    });
  }

  /**
   * Get effective opening hours for a location.
   * Returns custom hours if set, otherwise user's default hours.
   */
  public function getEffectiveOpeningHours($locationId)
  {
    $location = $this->locations()->find($locationId);

    if ($location && !empty($location->opening_hours)) {
      return $location->opening_hours;
    }

    return $this->opening_hours ?? $this->getDefaultOpeningHours();
  }

  /**
   * Get default opening hours structure.
   */
  public function getDefaultOpeningHours()
  {
    return [
      'monday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
      'tuesday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
      'wednesday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
      'thursday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
      'friday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
      'saturday' => ['is_open' => false, 'open' => '09:00', 'close' => '18:00'],
      'sunday' => ['is_open' => false, 'open' => '09:00', 'close' => '18:00'],
    ];
  }

  /**
   * Get all locations with their notification status
   */
  public function getLocationsWithNotificationStatus()
  {
    return $this->locations()->get()->map(function ($location) {
      return [
        'id' => $location->id,
        'location' => $location, // Direct location object instead of masterLocation relation
        'street' => $location->street_address,
        'house_number' => $location->additional_address,
        'city' => $location->city,
        'postal_code' => $location->postal_code,
        'country' => $location->country,
        'notification_email' => $location->notification_email,
        'use_custom_notifications' => $location->use_custom_notifications,
        'effective_email' => $this->getNotificationEmailForLocation($location->id),
        'status' => $location->use_custom_notifications ? 'Eigene' : 'Standard',
      ];
    });
  }

  /**
   * Get all locations with their contact details status
   */
  public function getLocationsWithContactStatusAttribute()
  {
    return $this->locations()->with(['contactDetails', 'country'])->get()->map(function ($location) {
      $contactDetails = $location->contactDetails;
      $hasCustomDetails = $contactDetails && $contactDetails->use_custom_contact_details;

      return [
        'id' => $location->id,
        'name' => $location->name,
        'city' => $location->city,
        'postal_code' => $location->postal_code,
        'country' => $location->country->name ?? 'Deutschland',
        'street_address' => $location->street_address,
        'additional_address' => $location->additional_address,
        'has_custom_contact' => $hasCustomDetails,
        'contact_status' => $hasCustomDetails ? 'Eigene' : 'Standard',
        'contact_details' => $contactDetails,
      ];
    });
  }

  // Role Management Methods (Spatie Integration)
  protected $guard_name = 'web'; // Default guard for Spatie

  /**
   * Check if user has a protected system role
   */
  public function hasProtectedRole(): bool
  {
    return $this->hasAnyRole(['admin', 'vendor', 'user', 'guest']);
  }

  /**
   * Safe role assignment that preserves protected roles
   */
  public function assignSafeRole($role): void
  {
    $protectedRoles = ['admin', 'vendor', 'user', 'guest'];

    // If user has no protected role, allow assignment
    if (!$this->hasAnyRole($protectedRoles)) {
      $this->assignRole($role);
    }
  }

  /**
   * Get user's primary role (first role)
   */
  public function getPrimaryRole()
  {
    return $this->roles->first();
  }

  /**
   * Get user's role display name with color
   */
  public function getRoleDisplayAttribute()
  {
    $role = $this->getPrimaryRole();
    if ($role) {
      return [
        'name' => $role->name,
        'color' => $role->color ?? '#007bff',
        'is_protected' => $role->is_protected ?? false
      ];
    }
    return ['name' => 'No Role', 'color' => '#6c757d', 'is_protected' => false];
  }

  /**
   * Check if user can be assigned roles (admins can assign, but not to other admins)
   */
  public function canAssignRoles(): bool
  {
    return $this->hasRole('admin');
  }

  /**
   * Check if this user's roles can be modified
   */
  public function canModifyRoles(): bool
  {
    // Protected users (admins) should be handled carefully
    return !$this->hasRole('admin') || auth()->user()->hasRole('admin');
  }

  // Two-Factor Authentication Methods

  /**
   * Check if user has 2FA enabled and confirmed
   */
  public function hasTwoFactorEnabled(): bool
  {
    return $this->two_factor_enabled && $this->two_factor_confirmed_at;
  }

  /**
   * Get the user's 2FA secret (decrypted)
   */
  public function getTwoFactorSecretAttribute($value): ?string
  {
    return $value ? Crypt::decryptString($value) : null;
  }

  /**
   * Set the user's 2FA secret (encrypted)
   */
  public function setTwoFactorSecretAttribute($value): void
  {
    $this->attributes['two_factor_secret'] = $value ? Crypt::encryptString($value) : null;
  }

  /**
   * Generate new recovery codes
   */
  public function generateRecoveryCodes(): array
  {
    $codes = [];
    for ($i = 0; $i < 8; $i++) {
      $codes[] = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
    }
    
    $this->two_factor_recovery_codes = $codes;
    $this->save();
    
    return $codes;
  }

  /**
   * Use a recovery code
   */
  public function useRecoveryCode(string $code): bool
  {
    $codes = $this->two_factor_recovery_codes ?? [];
    $index = array_search(strtoupper($code), $codes);
    
    if ($index !== false) {
      unset($codes[$index]);
      $this->two_factor_recovery_codes = array_values($codes);
      $this->save();
      return true;
    }
    
    return false;
  }
}
