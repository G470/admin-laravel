<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\User;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Review;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with comprehensive statistics and analytics
     */
    public function index()
    {
        // Core Platform Statistics
        $stats = $this->getCorePlatformStats();
        
        // Time-based Analytics
        $analytics = $this->getTimeBasedAnalytics();
        
        // Recent Activity Data
        $recentActivity = $this->getRecentActivity();
        
        // Performance Metrics
        $performance = $this->getPerformanceMetrics();
        
        // System Health Indicators
        $systemHealth = $this->getSystemHealth();

        return view('content.admin.dashboard', compact(
            'stats',
            'analytics', 
            'recentActivity',
            'performance',
            'systemHealth'
        ));
    }

    /**
     * Get core platform statistics
     */
    private function getCorePlatformStats()
    {
        $totalUsers = User::count();
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $newUsersLastWeek = User::whereBetween('created_at', [Carbon::now()->subWeeks(2), Carbon::now()->subWeek()])->count();
        $userGrowthPercentage = $newUsersLastWeek > 0 ? (($newUsersThisWeek - $newUsersLastWeek) / $newUsersLastWeek) * 100 : 0;

        $totalVendors = User::where('is_vendor', true)->count();
        $activeVendors = User::where('is_vendor', true)
            ->whereHas('rentals', function($q) {
                $q->where('rentals.status', 'online');
            })->count();

        $totalRentals = Rental::count();
        $activeRentals = Rental::where('status', 'online')->count();
        $pendingRentals = Rental::where('status', 'pending')->count();

        $totalBookings = Booking::count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        $totalRevenue = Booking::where('status', 'completed')->sum('total_amount');
        $thisMonthRevenue = Booking::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');
        
        $lastMonthRevenue = Booking::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total_amount');
        
        $revenueGrowthPercentage = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        return [
            'total_users' => $totalUsers,
            'new_users_this_week' => $newUsersThisWeek,
            'user_growth_percentage' => round($userGrowthPercentage, 1),
            'total_vendors' => $totalVendors,
            'active_vendors' => $activeVendors,
            'vendor_activation_rate' => $totalVendors > 0 ? round(($activeVendors / $totalVendors) * 100, 1) : 0,
            'total_rentals' => $totalRentals,
            'active_rentals' => $activeRentals,
            'pending_rentals' => $pendingRentals,
            'rental_approval_rate' => $totalRentals > 0 ? round(($activeRentals / $totalRentals) * 100, 1) : 0,
            'total_bookings' => $totalBookings,
            'completed_bookings' => $completedBookings,
            'pending_bookings' => $pendingBookings,
            'booking_completion_rate' => $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) : 0,
            'total_revenue' => $totalRevenue,
            'this_month_revenue' => $thisMonthRevenue,
            'revenue_growth_percentage' => round($revenueGrowthPercentage, 1),
        ];
    }

    /**
     * Get time-based analytics for charts
     */
    private function getTimeBasedAnalytics()
    {
        // Monthly revenue for the current year
        $monthlyRevenue = Booking::where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months with 0
        $completeMonthlyRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $completeMonthlyRevenue[$i] = $monthlyRevenue[$i] ?? 0;
        }

        // Daily registrations for the last 30 days
        $dailyRegistrations = User::where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Weekly bookings for the last 12 weeks
        $weeklyBookings = Booking::where('created_at', '>=', Carbon::now()->subWeeks(12))
            ->select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('week', 'year')
            ->get();

        return [
            'monthly_revenue' => $completeMonthlyRevenue,
            'daily_registrations' => $dailyRegistrations,
            'weekly_bookings' => $weeklyBookings,
        ];
    }

    /**
     * Get recent activity data
     */
    private function getRecentActivity()
    {
        $recentUsers = User::with('roles')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'is_vendor' => $user->is_vendor,
                    'is_admin' => $user->is_admin,
                    'status' => $user->email_verified_at ? 'verified' : 'unverified'
                ];
            });

        $recentRentals = Rental::with(['vendor', 'category', 'location'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($rental) {
                return [
                    'id' => $rental->id,
                    'name' => $rental->name,
                    'vendor_name' => $rental->vendor->name ?? 'Unknown',
                    'category_name' => $rental->category->name ?? 'Uncategorized',
                    'location' => $rental->location->city ?? 'No location',
                    'price' => $rental->price,
                    'status' => $rental->status,
                    'created_at' => $rental->created_at
                ];
            });

        $recentBookings = Booking::with(['renter', 'rental'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'renter_name' => $booking->renter->name ?? 'Unknown',
                    'rental_name' => $booking->rental->name ?? 'Unknown',
                    'total_amount' => $booking->total_amount,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date
                ];
            });

        $recentReviews = Review::with(['user', 'rental'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user->name ?? 'Anonymous',
                    'rental_name' => $review->rental->name ?? 'Unknown',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at
                ];
            });

        return [
            'recent_users' => $recentUsers,
            'recent_rentals' => $recentRentals,
            'recent_bookings' => $recentBookings,
            'recent_reviews' => $recentReviews,
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        // Top categories by rental count
        $topCategories = Category::withCount('rentals')
            ->orderBy('rentals_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->name,
                    'rentals_count' => $category->rentals_count,
                    'percentage' => 0 // Will be calculated in view
                ];
            });

        // Top vendors by revenue
        $topVendors = User::where('is_vendor', true)
            ->withSum(['vendorBookings' => function($q) {
                $q->where('bookings.status', 'completed');
            }], 'total_amount')
            ->orderBy('vendor_bookings_sum_total_amount', 'desc')
            ->take(5)
            ->get()
            ->map(function($vendor) {
                return [
                    'name' => $vendor->name,
                    'total_revenue' => $vendor->vendor_bookings_sum_total_amount ?? 0,
                    'rentals_count' => $vendor->rentals()->count()
                ];
            });

        // Average rating
        $averageRating = Review::avg('rating') ?? 0;
        $totalReviews = Review::count();

        // Most popular locations
        $topLocations = Location::withCount(['rentals' => function($q) {
                $q->where('rentals.status', 'online');
            }])
            ->orderBy('rentals_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($location) {
                return [
                    'city' => $location->city,
                    'postcode' => $location->postcode,
                    'rentals_count' => $location->rentals_count
                ];
            });

        return [
            'top_categories' => $topCategories,
            'top_vendors' => $topVendors,
            'average_rating' => round($averageRating, 1),
            'total_reviews' => $totalReviews,
            'top_locations' => $topLocations,
        ];
    }

    /**
     * Get system health indicators
     */
    private function getSystemHealth()
    {
        // Database health
        $dbConnections = DB::select('SHOW STATUS LIKE "Threads_connected"');
        $dbThreadsConnected = $dbConnections[0]->Value ?? 0;

        // Storage health
        $storageUsed = 0;
        $storageTotal = disk_total_space(storage_path());
        $storageFree = disk_free_space(storage_path());
        $storageUsagePercentage = $storageTotal > 0 ? (($storageTotal - $storageFree) / $storageTotal) * 100 : 0;

        // Error rates (last 24 hours)
        $errorCount = 0; // This would typically come from logs
        
        // Active users (last 24 hours)
        $activeUsers = User::where('last_login_at', '>=', Carbon::now()->subDay())->count();

        return [
            'db_connections' => $dbThreadsConnected,
            'storage_usage_percentage' => round($storageUsagePercentage, 1),
            'storage_free_gb' => round($storageFree / (1024 * 1024 * 1024), 2),
            'error_count_24h' => $errorCount,
            'active_users_24h' => $activeUsers,
            'system_status' => 'healthy', // This would be calculated based on various factors
        ];
    }

    /**
     * API endpoint for real-time dashboard statistics
     */
    public function getStats(Request $request)
    {
        try {
            $stats = $this->getCorePlatformStats();
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API endpoint for real-time system health
     */
    public function getSystemHealthApi(Request $request)
    {
        try {
            $health = $this->getSystemHealth();
            
            return response()->json([
                'success' => true,
                'health' => $health,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch system health',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API endpoint for recent activity updates
     */
    public function getRecentActivityApi(Request $request)
    {
        try {
            $activity = $this->getRecentActivity();
            
            return response()->json([
                'success' => true,
                'activity' => $activity,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent activity',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}