<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Opening extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
        'break_start',
        'break_end',
        'notes'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
    ];

    /**
     * Days of the week mapping
     */
    public static $daysOfWeek = [
        0 => 'Sonntag',
        1 => 'Montag',
        2 => 'Dienstag',
        3 => 'Mittwoch',
        4 => 'Donnerstag',
        5 => 'Freitag',
        6 => 'Samstag'
    ];

    /**
     * Get the location that this opening belongs to
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the day name in German
     */
    public function getDayNameAttribute()
    {
        return self::$daysOfWeek[$this->day_of_week] ?? 'Unbekannt';
    }

    /**
     * Check if the location is open at a specific time
     */
    public function isOpenAt($time)
    {
        if ($this->is_closed) {
            return false;
        }

        $time = Carbon::parse($time);
        $openTime = Carbon::parse($this->open_time);
        $closeTime = Carbon::parse($this->close_time);

        // Handle overnight openings (e.g., 23:00 - 02:00)
        if ($closeTime->lessThan($openTime)) {
            return $time->greaterThanOrEqualTo($openTime) || $time->lessThanOrEqualTo($closeTime);
        }

        // Check if it's during break time
        if ($this->break_start && $this->break_end) {
            $breakStart = Carbon::parse($this->break_start);
            $breakEnd = Carbon::parse($this->break_end);
            
            if ($time->between($breakStart, $breakEnd)) {
                return false;
            }
        }

        return $time->between($openTime, $closeTime);
    }

    /**
     * Get formatted opening hours string
     */
    public function getFormattedHoursAttribute()
    {
        if ($this->is_closed) {
            return 'Geschlossen';
        }

        $hours = Carbon::parse($this->open_time)->format('H:i') . ' - ' . Carbon::parse($this->close_time)->format('H:i');
        
        if ($this->break_start && $this->break_end) {
            $hours .= ' (Pause: ' . Carbon::parse($this->break_start)->format('H:i') . ' - ' . Carbon::parse($this->break_end)->format('H:i') . ')';
        }

        return $hours;
    }

    /**
     * Scope to get openings for a specific day of week
     */
    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope to get only open days (not closed)
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    /**
     * Scope to get openings for a specific location
     */
    public function scopeForLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Get opening hours for the current day
     */
    public static function getTodaysHours($locationId)
    {
        $dayOfWeek = Carbon::now()->dayOfWeek;
        
        return self::where('location_id', $locationId)
                   ->where('day_of_week', $dayOfWeek)
                   ->first();
    }

    /**
     * Check if location is currently open
     */
    public static function isCurrentlyOpen($locationId)
    {
        $todaysHours = self::getTodaysHours($locationId);
        
        if (!$todaysHours) {
            return false;
        }

        return $todaysHours->isOpenAt(Carbon::now());
    }

    /**
     * Get next opening time for a location
     */
    public static function getNextOpening($locationId)
    {
        $now = Carbon::now();
        $currentDayOfWeek = $now->dayOfWeek;
        
        // Check remaining time today
        $todaysHours = self::getTodaysHours($locationId);
        if ($todaysHours && !$todaysHours->is_closed) {
            $openTime = Carbon::parse($todaysHours->open_time);
            if ($now->lessThan($openTime)) {
                return $now->copy()->setTimeFrom($openTime);
            }
        }
        
        // Check next 7 days
        for ($i = 1; $i <= 7; $i++) {
            $checkDay = ($currentDayOfWeek + $i) % 7;
            $opening = self::where('location_id', $locationId)
                          ->where('day_of_week', $checkDay)
                          ->where('is_closed', false)
                          ->first();
            
            if ($opening) {
                return $now->copy()->addDays($i)->setTimeFrom(Carbon::parse($opening->open_time));
            }
        }
        
        return null;
    }
}
