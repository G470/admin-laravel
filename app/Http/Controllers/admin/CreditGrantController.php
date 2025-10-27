<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorCredit;
use App\Models\CreditPackage;
use App\Models\AdminCreditGrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditGrantController extends Controller
{
    /**
     * Display a listing of credit grants
     */
    public function index()
    {
        return view('content.admin.credit-grants.index');
    }

    /**
     * Show the form for creating a new credit grant
     */
    public function create()
    {
        $vendors = User::where('is_vendor', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $creditPackages = CreditPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'credits_amount', 'standard_price']);

        return view('content.admin.credit-grants.create', compact('vendors', 'creditPackages'));
    }

    /**
     * Store a newly created credit grant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'credit_package_id' => 'required|exists:credit_packages,id',
            'credits_amount' => 'required|integer|min:1|max:10000',
            'grant_type' => 'required|in:admin_grant,compensation,bonus,correction',
            'reason' => 'required|string|max:500',
            'internal_note' => 'nullable|string|max:1000',
            'grant_date' => 'required|date|before_or_equal:now',
        ]);

        // Verify vendor is actually a vendor
        $vendor = User::where('id', $validated['vendor_id'])
            ->where('is_vendor', true)
            ->firstOrFail();

        // Get credit package for reference
        $creditPackage = CreditPackage::findOrFail($validated['credit_package_id']);

        DB::beginTransaction();

        try {
            // Create admin credit grant record
            $creditGrant = AdminCreditGrant::create([
                'admin_id' => auth()->id(),
                'vendor_id' => $validated['vendor_id'],
                'credit_package_id' => $validated['credit_package_id'],
                'credits_granted' => $validated['credits_amount'],
                'grant_type' => $validated['grant_type'],
                'reason' => $validated['reason'],
                'internal_note' => $validated['internal_note'],
                'grant_date' => $validated['grant_date'],
                'status' => 'completed'
            ]);

            // Create vendor credit record (similar to purchase but with admin grant)
            $vendorCredit = VendorCredit::create([
                'vendor_id' => $validated['vendor_id'],
                'credit_package_id' => $validated['credit_package_id'],
                'credits_purchased' => $validated['credits_amount'],
                'credits_remaining' => $validated['credits_amount'],
                'amount_paid' => 0.00, // Free credits
                'payment_status' => 'completed',
                'payment_reference' => 'ADMIN_GRANT_' . time() . '_' . ($creditGrant->id ?? 'temp'),
                'payment_provider' => 'admin_grant',
                'purchased_at' => $validated['grant_date']
            ]);

            // Log the action
            Log::info('Admin credit grant created', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'credits_granted' => $validated['credits_amount'],
                'grant_type' => $validated['grant_type'],
                'reason' => $validated['reason'],
                'credit_grant_id' => $creditGrant->id
            ]);

            DB::commit();

            return redirect()->route('admin.credit-grants.index')
                ->with('success', "{$validated['credits_amount']} Credits wurden erfolgreich an {$vendor->name} vergeben.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin credit grant failed', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'vendor_id' => $validated['vendor_id'],
                'credits_amount' => $validated['credits_amount']
            ]);

            return back()->withInput()
                ->with('error', 'Fehler bei der Credit-Vergabe. Bitte versuchen Sie es erneut.');
        }
    }

    /**
     * Display the specified credit grant
     */
    public function show(AdminCreditGrant $creditGrant)
    {
        $creditGrant->load(['admin', 'vendor', 'creditPackage']);

        return view('content.admin.credit-grants.show', compact('creditGrant'));
    }

    /**
     * Show the form for editing the specified credit grant
     */
    public function edit(AdminCreditGrant $creditGrant)
    {
        // Only allow editing if not completed
        if ($creditGrant->status === 'completed') {
            return redirect()->route('admin.credit-grants.show', $creditGrant)
                ->with('error', 'Abgeschlossene Credit-Vergaben können nicht bearbeitet werden.');
        }

        $vendors = User::where('is_vendor', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $creditPackages = CreditPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'credits_amount', 'standard_price']);

        return view('content.admin.credit-grants.edit', compact('creditGrant', 'vendors', 'creditPackages'));
    }

    /**
     * Update the specified credit grant
     */
    public function update(Request $request, AdminCreditGrant $creditGrant)
    {
        // Only allow updating if not completed
        if ($creditGrant->status === 'completed') {
            return redirect()->route('admin.credit-grants.show', $creditGrant)
                ->with('error', 'Abgeschlossene Credit-Vergaben können nicht bearbeitet werden.');
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'credit_package_id' => 'required|exists:credit_packages,id',
            'credits_amount' => 'required|integer|min:1|max:10000',
            'grant_type' => 'required|in:admin_grant,compensation,bonus,correction',
            'reason' => 'required|string|max:500',
            'internal_note' => 'nullable|string|max:1000',
            'grant_date' => 'required|date|before_or_equal:now',
        ]);

        DB::beginTransaction();

        try {
            $creditGrant->update($validated);

            Log::info('Admin credit grant updated', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'credit_grant_id' => $creditGrant->id,
                'vendor_id' => $validated['vendor_id'],
                'credits_granted' => $validated['credits_amount']
            ]);

            DB::commit();

            return redirect()->route('admin.credit-grants.show', $creditGrant)
                ->with('success', 'Credit-Vergabe wurde erfolgreich aktualisiert.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin credit grant update failed', [
                'error' => $e->getMessage(),
                'credit_grant_id' => $creditGrant->id
            ]);

            return back()->withInput()
                ->with('error', 'Fehler beim Aktualisieren der Credit-Vergabe.');
        }
    }

    /**
     * Remove the specified credit grant
     */
    public function destroy(AdminCreditGrant $creditGrant)
    {
        // Only allow deletion if not completed
        if ($creditGrant->status === 'completed') {
            return redirect()->route('admin.credit-grants.show', $creditGrant)
                ->with('error', 'Abgeschlossene Credit-Vergaben können nicht gelöscht werden.');
        }

        try {
            $creditGrant->delete();

            Log::info('Admin credit grant deleted', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'credit_grant_id' => $creditGrant->id
            ]);

            return redirect()->route('admin.credit-grants.index')
                ->with('success', 'Credit-Vergabe wurde erfolgreich gelöscht.');

        } catch (\Exception $e) {
            Log::error('Admin credit grant deletion failed', [
                'error' => $e->getMessage(),
                'credit_grant_id' => $creditGrant->id
            ]);

            return back()->with('error', 'Fehler beim Löschen der Credit-Vergabe.');
        }
    }

    /**
     * Get statistics for credit grants
     */
    public function statistics()
    {
        $stats = [
            'total_grants' => AdminCreditGrant::count(),
            'total_credits_granted' => AdminCreditGrant::sum('credits_granted'),
            'grants_this_month' => AdminCreditGrant::whereMonth('created_at', now()->month)->count(),
            'credits_this_month' => AdminCreditGrant::whereMonth('created_at', now()->month)->sum('credits_granted'),
            'top_grant_types' => AdminCreditGrant::selectRaw('grant_type, COUNT(*) as count, SUM(credits_granted) as total_credits')
                ->groupBy('grant_type')
                ->orderBy('total_credits', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Export credit grants
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = AdminCreditGrant::with(['admin', 'vendor', 'creditPackage']);

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $creditGrants = $query->orderBy('created_at', 'desc')->get();

        // Transform data for export
        $exportData = $creditGrants->map(function ($grant) {
            return [
                'ID' => $grant->id,
                'Admin' => $grant->admin->name,
                'Vendor' => $grant->vendor->name,
                'Vendor Email' => $grant->vendor->email,
                'Credits' => $grant->credits_granted,
                'Grant Type' => $this->getGrantTypeLabel($grant->grant_type),
                'Reason' => $grant->reason,
                'Date' => $grant->grant_date->format('d.m.Y'),
                'Status' => $this->getStatusLabel($grant->status)
            ];
        });

        if ($format === 'csv') {
            return $this->exportToCsv($exportData, 'credit_grants_' . now()->format('Y-m-d'));
        }

        return response()->json(['error' => 'Unsupported format']);
    }

    /**
     * Get grant type label
     */
    private function getGrantTypeLabel($type)
    {
        return [
            'admin_grant' => 'Admin-Vergabe',
            'compensation' => 'Entschädigung',
            'bonus' => 'Bonus',
            'correction' => 'Korrektur'
        ][$type] ?? $type;
    }

    /**
     * Get status label
     */
    private function getStatusLabel($status)
    {
        return [
            'pending' => 'Ausstehend',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Abgebrochen'
        ][$status] ?? $status;
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Add headers
            if ($data->count() > 0) {
                fputcsv($file, array_keys($data->first()));
            }

            // Add data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}