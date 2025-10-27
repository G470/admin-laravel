<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class AdminCreditGrant extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'credit_package_id',
        'credits_granted',
        'grant_type',
        'reason',
        'internal_note',
        'grant_date',
        'status'
    ];

    protected $casts = [
        'grant_date' => 'datetime',
        'credits_granted' => 'integer'
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function creditPackage()
    {
        return $this->belongsTo(CreditPackage::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeByGrantType($query, $grantType)
    {
        return $query->where('grant_type', $grantType);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('grant_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getGrantTypeLabelAttribute()
    {
        return [
            'admin_grant' => 'Admin-Vergabe',
            'compensation' => 'Entschädigung',
            'bonus' => 'Bonus',
            'correction' => 'Korrektur'
        ][$this->grant_type] ?? $this->grant_type;
    }

    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Ausstehend',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Abgebrochen'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    public function getGrantTypeColorAttribute()
    {
        return [
            'admin_grant' => 'primary',
            'compensation' => 'warning',
            'bonus' => 'success',
            'correction' => 'info'
        ][$this->grant_type] ?? 'secondary';
    }

    public function getFormattedGrantDateAttribute()
    {
        return $this->grant_date->format('d.m.Y H:i');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d.m.Y H:i');
    }

    // Methods
    public function canBeEdited()
    {
        return $this->status !== 'completed';
    }

    public function canBeDeleted()
    {
        return $this->status !== 'completed';
    }

    public function getMonetaryValue()
    {
        if ($this->creditPackage) {
            $creditValue = $this->creditPackage->standard_price / $this->creditPackage->credits_amount;
            return $this->credits_granted * $creditValue;
        }
        return 0;
    }

    public function getFormattedMonetaryValueAttribute()
    {
        return number_format($this->getMonetaryValue(), 2, ',', '.') . ' €';
    }

    // Static methods
    public static function getGrantTypeOptions()
    {
        return [
            'admin_grant' => 'Admin-Vergabe',
            'compensation' => 'Entschädigung',
            'bonus' => 'Bonus',
            'correction' => 'Korrektur'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'pending' => 'Ausstehend',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Abgebrochen'
        ];
    }

    public static function getStatistics()
    {
        return [
            'total_grants' => self::count(),
            'total_credits_granted' => self::sum('credits_granted'),
            'grants_this_month' => self::whereMonth('created_at', now()->month)->count(),
            'credits_this_month' => self::whereMonth('created_at', now()->month)->sum('credits_granted'),
            'grants_this_year' => self::whereYear('created_at', now()->year)->count(),
            'credits_this_year' => self::whereYear('created_at', now()->year)->sum('credits_granted'),
            'average_credits_per_grant' => self::avg('credits_granted'),
            'top_grant_types' => self::selectRaw('grant_type, COUNT(*) as count, SUM(credits_granted) as total_credits')
                ->groupBy('grant_type')
                ->orderBy('total_credits', 'desc')
                ->limit(5)
                ->get()
        ];
    }

    public static function getVendorStatistics($vendorId)
    {
        return [
            'total_grants' => self::where('vendor_id', $vendorId)->count(),
            'total_credits_granted' => self::where('vendor_id', $vendorId)->sum('credits_granted'),
            'grants_this_month' => self::where('vendor_id', $vendorId)
                ->whereMonth('created_at', now()->month)->count(),
            'credits_this_month' => self::where('vendor_id', $vendorId)
                ->whereMonth('created_at', now()->month)->sum('credits_granted'),
            'grants_this_year' => self::where('vendor_id', $vendorId)
                ->whereYear('created_at', now()->year)->count(),
            'credits_this_year' => self::where('vendor_id', $vendorId)
                ->whereYear('created_at', now()->year)->sum('credits_granted'),
            'grant_types' => self::where('vendor_id', $vendorId)
                ->selectRaw('grant_type, COUNT(*) as count, SUM(credits_granted) as total_credits')
                ->groupBy('grant_type')
                ->orderBy('total_credits', 'desc')
                ->get()
        ];
    }

    public static function getAdminStatistics($adminId)
    {
        return [
            'total_grants' => self::where('admin_id', $adminId)->count(),
            'total_credits_granted' => self::where('admin_id', $adminId)->sum('credits_granted'),
            'grants_this_month' => self::where('admin_id', $adminId)
                ->whereMonth('created_at', now()->month)->count(),
            'credits_this_month' => self::where('admin_id', $adminId)
                ->whereMonth('created_at', now()->month)->sum('credits_granted'),
            'grants_this_year' => self::where('admin_id', $adminId)
                ->whereYear('created_at', now()->year)->count(),
            'credits_this_year' => self::where('admin_id', $adminId)
                ->whereYear('created_at', now()->year)->sum('credits_granted'),
            'grant_types' => self::where('admin_id', $adminId)
                ->selectRaw('grant_type, COUNT(*) as count, SUM(credits_granted) as total_credits')
                ->groupBy('grant_type')
                ->orderBy('total_credits', 'desc')
                ->get()
        ];
    }
}