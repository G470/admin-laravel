<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        // Get statistics for dashboard
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'inactive_users' => User::whereNull('email_verified_at')->count(),
            'vendors' => User::where('is_vendor', true)->count(),
            'admins' => User::where('is_admin', true)->count(),
            'new_users_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
        ];

        return view('content.admin.users', compact('users', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_admin' => 'boolean',
            'is_vendor' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'vat_number' => 'nullable|string|max:50',
            'bank_account' => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Set default boolean values if not provided
        $validated['is_admin'] = $validated['is_admin'] ?? false;
        $validated['is_vendor'] = $validated['is_vendor'] ?? false;

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $user = User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Benutzer wurde erfolgreich erstellt.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_admin' => 'boolean',
            'is_vendor' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'vat_number' => 'nullable|string|max:50',
            'bank_account' => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Set boolean values 
        $validated['is_admin'] = $validated['is_admin'] ?? false;
        $validated['is_vendor'] = $validated['is_vendor'] ?? false;

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Benutzer wurde erfolgreich aktualisiert.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Sie können Ihren eigenen Account nicht löschen.');
        }

        // Check if user has any related data before deletion
        // This is a basic check - you may want to add more specific relationship checks
        
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Benutzer wurde erfolgreich gelöscht.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Sie können Ihren eigenen Account-Status nicht ändern.');
        }

        // Toggle email verification status as a way to activate/deactivate users
        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now()
        ]);

        $status = $user->email_verified_at ? 'aktiviert' : 'deaktiviert';
        return redirect()->route('admin.users.index')
            ->with('success', "Benutzer wurde erfolgreich {$status}.");
    }

    public function create()
    {
        // TODO: Create user creation view
        return redirect()->route('admin.users.index')
            ->with('info', 'User creation form coming soon.');
    }

    public function show(User $user)
    {
        // TODO: Create user detail view
        return redirect()->route('admin.users.index')
            ->with('info', 'User detail view coming soon.');
    }

    public function edit(User $user)
    {
        // TODO: Create user edit view
        return redirect()->route('admin.users.index')
            ->with('info', 'User edit form coming soon.');
    }
}