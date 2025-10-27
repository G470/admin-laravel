<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        // Note: Using 'admin' middleware instead of 'role:admin' for compatibility
        // The admin middleware checks Auth::user()->isAdmin() which includes role checks
    }

    /**
     * Display a listing of the permissions.
     */
    public function index(Request $request)
    {
        $query = Permission::query()->withCount('roles');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by group
        if ($request->filled('group_filter')) {
            $query->where('group', $request->group_filter);
        }

        // Filter by guard
        if ($request->filled('guard_filter')) {
            $query->where('guard_name', $request->guard_filter);
        }

        $permissions = $query->orderBy('group')->orderBy('name')->paginate(20);
        $guards = Permission::distinct()->pluck('guard_name')->filter();
        $groups = $this->getPermissionGroups();

        return view('content.admin.permissions.index', compact('permissions', 'guards', 'groups'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        $guards = ['web' => 'Web', 'api' => 'API'];
        $existingGroups = $this->getPermissionGroups();

        return view('content.admin.permissions.create', compact('guards', 'existingGroups'));
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'guard_name' => ['required', 'string', Rule::in(['web', 'api'])],
            'group' => ['nullable', 'string', 'max:255'],
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => $request->guard_name,
            'group' => $request->group,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->name}' created successfully.");
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        $rolesByGroup = $permission->roles->groupBy(function ($role) {
            return in_array($role->name, ['admin', 'vendor', 'user', 'guest']) ? 'System Roles' : 'Custom Roles';
        });

        return view('content.admin.permissions.show', compact('permission', 'rolesByGroup'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        $guards = ['web' => 'Web', 'api' => 'API'];
        $existingGroups = $this->getPermissionGroups();

        return view('content.admin.permissions.edit', compact('permission', 'guards', 'existingGroups'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'group' => ['nullable', 'string', 'max:255'],
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->name}' updated successfully.");
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            $roleNames = $permission->roles->pluck('name')->implode(', ');
            return redirect()->route('admin.permissions.index')
                ->with('error', "Cannot delete permission '{$permission->name}' because it is assigned to roles: {$roleNames}");
        }

        $permissionName = $permission->name;
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permissionName}' deleted successfully.");
    }

    /**
     * Bulk assign permission to roles
     */
    public function assignToRoles(Request $request, Permission $permission)
    {
        $request->validate([
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id']
        ]);

        $roles = Role::whereIn('id', $request->roles ?? [])->get();

        // Sync permission to selected roles
        foreach (Role::all() as $role) {
            if ($roles->contains($role)) {
                $role->givePermissionTo($permission);
            } else {
                $role->revokePermissionTo($permission);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Permission '{$permission->name}' role assignments updated successfully."
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
     * Get permissions statistics
     */
    public function statistics()
    {
        $stats = [
            'total_permissions' => Permission::count(),
            'permissions_by_group' => Permission::selectRaw('group, COUNT(*) as count')
                ->whereNotNull('group')
                ->groupBy('group')
                ->orderBy('group')
                ->get(),
            'permissions_by_guard' => Permission::selectRaw('guard_name, COUNT(*) as count')
                ->groupBy('guard_name')
                ->get(),
            'unassigned_permissions' => Permission::doesntHave('roles')->count(),
        ];

        return view('content.admin.permissions.statistics', compact('stats'));
    }
}
