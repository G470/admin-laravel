<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CreditPackage;
use App\Models\VendorCredit;
use Illuminate\Http\Request;

class CreditPackageController extends Controller
{
    public function index()
    {
        $packages = CreditPackage::withCount([
            'purchases' => function ($query) {
                $query->where('payment_status', 'completed');
            }
        ])
            ->withSum([
                'purchases' => function ($query) {
                    $query->where('payment_status', 'completed');
                }
            ], 'amount_paid')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stats = [
            'total_packages' => CreditPackage::count(),
            'active_packages' => CreditPackage::active()->count(),
            'total_revenue' => VendorCredit::where('payment_status', 'completed')->sum('amount_paid'),
            'total_sales' => VendorCredit::where('payment_status', 'completed')->count()
        ];

        return view('content.admin.credit-packages', compact('packages', 'stats'));
    }

    public function create()
    {
        return view('content.admin.credit-packages-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(CreditPackage::rules());

        CreditPackage::create($validated);

        return redirect()->route('admin.credit-packages.index')
            ->with('success', 'Credit-Paket erfolgreich erstellt!');
    }

    public function edit(CreditPackage $creditPackage)
    {
        return view('content.admin.credit-packages-edit', compact('creditPackage'));
    }

    public function update(Request $request, CreditPackage $creditPackage)
    {
        $validated = $request->validate(CreditPackage::rules($creditPackage->id));

        $creditPackage->update($validated);

        return redirect()->route('admin.credit-packages.index')
            ->with('success', 'Credit-Paket erfolgreich aktualisiert!');
    }

    public function destroy(CreditPackage $creditPackage)
    {
        // Check if package has active purchases
        if ($creditPackage->purchases()->exists()) {
            return back()->with('error', 'Credit-Paket kann nicht gelöscht werden - es gibt bereits Käufe.');
        }

        $creditPackage->delete();

        return redirect()->route('admin.credit-packages.index')
            ->with('success', 'Credit-Paket erfolgreich gelöscht!');
    }

    public function toggle(CreditPackage $creditPackage)
    {
        $creditPackage->update(['is_active' => !$creditPackage->is_active]);

        $status = $creditPackage->is_active ? 'aktiviert' : 'deaktiviert';

        return response()->json([
            'success' => true,
            'message' => "Credit-Paket wurde {$status}.",
            'is_active' => $creditPackage->is_active
        ]);
    }

    public function duplicate(CreditPackage $creditPackage)
    {
        $duplicate = $creditPackage->replicate();
        $duplicate->name = $creditPackage->name . ' (Kopie)';
        $duplicate->is_active = false;
        $duplicate->save();

        return redirect()->route('admin.credit-packages.edit', $duplicate)
            ->with('success', 'Credit-Paket wurde dupliziert!');
    }

    public function analytics()
    {
        $packages = CreditPackage::withCount('vendorCredits')
            ->get()
            ->map(function ($package) {
                $package->total_purchases = $package->getTotalPurchases();
                $package->total_revenue = $package->getTotalRevenue();
                $package->popularity_score = $package->getPopularityScore();
                return $package;
            });

        $monthlyStatsRaw = VendorCredit::where('payment_status', 'completed')
            ->selectRaw('
                MONTH(purchased_at) as month,
                YEAR(purchased_at) as year,
                COUNT(*) as total_sales,
                SUM(amount_paid) as total_revenue,
                SUM(credits_purchased) as total_credits,
                COUNT(DISTINCT vendor_id) as new_vendors
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $monthlyStats = $monthlyStatsRaw->map(function ($item, $index) use ($monthlyStatsRaw) {
            $item->month_name = date('F', mktime(0, 0, 0, $item->month, 1));
            $item->avg_order_value = $item->total_sales > 0 ? $item->total_revenue / $item->total_sales : 0;

            // Calculate growth rate compared to previous month
            if ($index < count($monthlyStatsRaw) - 1) {
                $previousMonth = $monthlyStatsRaw[$index + 1] ?? null;
                if ($previousMonth && $previousMonth->total_revenue > 0) {
                    $item->growth_rate = (($item->total_revenue - $previousMonth->total_revenue) / $previousMonth->total_revenue) * 100;
                } else {
                    $item->growth_rate = 0;
                }
            } else {
                $item->growth_rate = 0;
            }

            return $item;
        });

        // Overall statistics
        $totalRevenue = VendorCredit::where('payment_status', 'completed')->sum('amount_paid');
        $totalCredits = VendorCredit::where('payment_status', 'completed')->sum('credits_purchased');
        $activeVendors = VendorCredit::where('payment_status', 'completed')
            ->where('credits_remaining', '>', 0)
            ->distinct('vendor_id')
            ->count();

        // Growth calculations (comparing current month to previous)
        $currentMonth = $monthlyStats->first();
        $previousMonth = $monthlyStats->skip(1)->first();

        $revenueGrowth = 0;
        $creditsGrowth = 0;
        $vendorGrowth = 0;

        if ($currentMonth && $previousMonth) {
            if ($previousMonth->total_revenue > 0) {
                $revenueGrowth = (($currentMonth->total_revenue - $previousMonth->total_revenue) / $previousMonth->total_revenue) * 100;
            }
            if ($previousMonth->total_credits > 0) {
                $creditsGrowth = (($currentMonth->total_credits - $previousMonth->total_credits) / $previousMonth->total_credits) * 100;
            }
            if ($previousMonth->new_vendors > 0) {
                $vendorGrowth = (($currentMonth->new_vendors - $previousMonth->new_vendors) / $previousMonth->new_vendors) * 100;
            }
        }

        // Conversion rate (dummy calculation - you'd need actual visitor data)
        $conversionRate = 15.5; // Placeholder
        $conversionGrowth = 2.3; // Placeholder

        // Top vendors
        $topVendors = VendorCredit::where('payment_status', 'completed')
            ->selectRaw('
                vendor_id,
                COUNT(*) as purchase_count,
                SUM(amount_paid) as total_spent,
                SUM(credits_purchased) as total_credits
            ')
            ->with('vendor:id,name')
            ->groupBy('vendor_id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return view('content.admin.credit-analytics', compact(
            'packages',
            'monthlyStats',
            'totalRevenue',
            'totalCredits',
            'activeVendors',
            'revenueGrowth',
            'creditsGrowth',
            'vendorGrowth',
            'conversionRate',
            'conversionGrowth',
            'topVendors'
        ));
    }
}