<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailChangeToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'new_email',
        'token',
        'expires_at',
        'used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Get the user that owns the token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the token is expired.
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the token is valid (not used and not expired).
     */
    public function isValid()
    {
        return !$this->used && !$this->isExpired();
    }

    /**
     * Mark the token as used.
     */
    public function markAsUsed()
    {
        $this->update(['used' => true]);
    }

    /**
     * Create a new email change token.
     */
    public static function createToken($userId, $newEmail, $expiresInHours = 24)
    {
        // Delete any existing unused tokens for this user
        self::where('user_id', $userId)
            ->where('used', false)
            ->delete();

        return self::create([
            'user_id' => $userId,
            'new_email' => $newEmail,
            'token' => Str::random(64),
            'expires_at' => Carbon::now()->addHours($expiresInHours),
            'used' => false,
        ]);
    }

    /**
     * Find a valid token by token string.
     */
    public static function findValidToken($token)
    {
        return self::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Clean up expired tokens.
     */
    public static function cleanupExpired()
    {
        return self::where('expires_at', '<', Carbon::now())
            ->orWhere('used', true)
            ->delete();
    }
}