<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Rental;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get admin dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_vendors' => User::role('vendor')->count(),
            'total_rentals' => Rental::count(),
            'total_bookings' => Booking::count(),
            'pending_approvals' => Rental::where('status', 'pending')->count(),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'top_categories' => $this->getTopCategories(),
        ];
    }

    /**
     * Get monthly revenue statistics
     */
    private function getMonthlyRevenue(): float
    {
        return Booking::where('status', 'completed')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_amount') ?? 0.0;
    }

    /**
     * Get top performing categories
     */
    private function getTopCategories(): array
    {
        return DB::table('rentals')
            ->join('categories', 'rentals.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as rental_count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('rental_count')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get recent activity for admin dashboard
     */
    public function getRecentActivity(int $limit = 10): array
    {
        return [
            'recent_users' => User::orderByDesc('created_at')->limit($limit)->get(),
            'recent_rentals' => Rental::with(['vendor', 'category'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get(),
            'recent_bookings' => Booking::with(['rental', 'user'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get(),
        ];
    }
}
