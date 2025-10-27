<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rental;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = User::role('vendor')->with(['rentals', 'bookings'])->get();
        return view('content.admin.vendors', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'company_name' => 'nullable|string|max:255',
            'company_registration' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $vendor = User::create($validated);
        $vendor->assignRole('vendor');

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vermieter wurde erfolgreich erstellt.');
    }

    public function update(Request $request, User $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $vendor->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'company_name' => 'nullable|string|max:255',
            'company_registration' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $vendor->update($validated);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vermieter wurde erfolgreich aktualisiert.');
    }

    public function destroy(User $vendor)
    {
        if ($vendor->rentals()->exists() || $vendor->bookings()->exists()) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'Dieser Vermieter kann nicht gelöscht werden, da er noch Vermietungsobjekte oder Buchungen hat.');
        }

        $vendor->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vermieter wurde erfolgreich gelöscht.');
    }

    public function toggleStatus(User $vendor)
    {
        $vendor->update([
            'status' => $vendor->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vermieterstatus wurde erfolgreich aktualisiert.');
    }

    public function rentals(User $vendor)
    {
        $rentals = $vendor->rentals()->with(['category', 'city'])->get();
        return view('content.admin.vendor-rentals', compact('vendor', 'rentals'));
    }

    public function bookings(User $vendor)
    {
        $bookings = $vendor->bookings()->with(['rental', 'renter'])->get();
        return view('content.admin.vendor-bookings', compact('vendor', 'bookings'));
    }

    public function earnings(User $vendor)
    {
        $earnings = $vendor->bookings()
            ->where('status', 'completed')
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                SUM(total_amount) as total_amount,
                SUM(commission_amount) as commission_amount,
                SUM(total_amount - commission_amount) as net_amount
            ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        return view('content.admin.vendor-earnings', compact('vendor', 'earnings'));
    }
}