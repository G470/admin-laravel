<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    protected $protectedRoles = ['admin', 'vendor', 'user', 'guest'];

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        // Note: Using 'admin' middleware instead of 'role:admin' for compatibility
        // The admin middleware checks Auth::user()->isAdmin() which includes role checks
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query()->withCount(['permissions', 'users']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by protection status
        if ($request->filled('protection_filter')) {
            $isProtected = $request->protection_filter === 'protected';
            if ($isProtected) {
                $query->whereIn('name', $this->protectedRoles);
            } else {
                $query->whereNotIn('name', $this->protectedRoles);
            }
        }

        // Filter by guard
        if ($request->filled('guard_filter')) {
            $query->where('guard_name', $request->guard_filter);
        }

        $roles = $query->orderBy('name')->paginate(15);
        $guards = Role::distinct()->pluck('guard_name')->filter();
        $permissionGroups = $this->getPermissionGroups();

        return view('content.admin.roles.index', compact('roles', 'guards', 'permissionGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        $guards = ['web' => 'Web', 'api' => 'API'];

        return view('content.admin.roles.create', compact('permissions', 'guards'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'guard_name' => ['required', 'string', Rule::in(['web', 'api'])],
            'color' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        // Prevent creation of protected role names
        if (in_array(strtolower($request->name), array_map('strtolower', $this->protectedRoles))) {
            return back()->withErrors(['name' => 'This role name is reserved by the system.'])->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => $request->guard_name,
            'color' => $request->color ?? '#007bff',
            'is_protected' => false
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->givePermissionTo($permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        $isProtected = in_array($role->name, $this->protectedRoles);

        // Group permissions by category
        $permissionsByGroup = $role->permissions->groupBy('group');

        return view('content.admin.roles.show', compact('role', 'isProtected', 'permissionsByGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Prevent editing protected roles
        if (in_array($role->name, $this->protectedRoles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Protected system roles cannot be modified.');
        }

        $permissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $guards = ['web' => 'Web', 'api' => 'API'];

        return view('content.admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'guards'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent updating protected roles
        if (in_array($role->name, $this->protectedRoles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Protected system roles cannot be modified.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        // Prevent renaming to protected role names
        if (in_array(strtolower($request->name), array_map('strtolower', $this->protectedRoles))) {
            return back()->withErrors(['name' => 'This role name is reserved by the system.'])->withInput();
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? $role->color,
        ]);

        // Update permissions
        $permissions = $request->has('permissions')
            ? Permission::whereIn('id', $request->permissions)->get()
            : collect();

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting protected roles
        if (in_array($role->name, $this->protectedRoles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Protected system roles cannot be deleted.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Cannot delete role '{$role->name}' because it has assigned users.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' deleted successfully.");
    }

    /**
     * Assign permissions to role via AJAX
     */
    public function assignPermissions(Request $request, Role $role)
    {
        // Prevent modifying protected roles
        if (in_array($role->name, $this->protectedRoles)) {
            return response()->json(['error' => 'Protected roles cannot be modified'], 403);
        }

        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        return response()->json([
            'success' => true,
            'message' => "Permissions updated for role '{$role->name}'"
        ]);
    }

    /**
     * Get permission groups for filtering
     */
    private function getPermissionGroups()
    {
        return Permission::select('group')
            ->distinct()
            ->whereNotNull('group')
            ->orderBy('group')
            ->pluck('group')
            ->toArray();
    }

    /**
     * Duplicate a role
     */
    public function duplicate(Role $role)
    {
        // Prevent duplicating protected roles
        if (in_array($role->name, $this->protectedRoles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Protected system roles cannot be duplicated.');
        }

        $newRoleName = $role->name . ' Copy';
        $counter = 1;

        while (Role::where('name', $newRoleName)->exists()) {
            $newRoleName = $role->name . ' Copy ' . $counter;
            $counter++;
        }

        $newRole = Role::create([
            'name' => $newRoleName,
            'description' => $role->description,
            'guard_name' => $role->guard_name,
            'color' => $role->color,
            'is_protected' => false
        ]);

        $newRole->givePermissionTo($role->permissions);

        return redirect()->route('admin.roles.edit', $newRole)
            ->with('success', "Role duplicated as '{$newRoleName}'. You can now modify it.");
    }
}
