<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('organization_id', auth()->user()->organization_id)->withCount('users')->get();
        return view('organization.roles.index', compact('roles'));
    }

    public function create()
    {
        $groupedPermissions = Permission::all()->groupBy('module');
        return view('organization.roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('organization.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        abort_if($role->organization_id !== auth()->user()->organization_id, 403);
        
        $groupedPermissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('organization.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->organization_id !== auth()->user()->organization_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('organization.roles.index')->with('success', 'Role updated successfully.');
    }

    public function show(Role $role)
    {
        abort_if($role->organization_id !== auth()->user()->organization_id, 403);
        $role->load('permissions');
        return view('organization.roles.show', compact('role'));
    }

    public function destroy(Role $role)
    {
        abort_if($role->organization_id !== auth()->user()->organization_id, 403);
        if ($role->users()->count() > 0) {
            return redirect()->route('organization.roles.index')->with('error', 'Cannot delete role assigned to users.');
        }
        $role->permissions()->detach();
        $role->delete();
        return redirect()->route('organization.roles.index')->with('success', 'Role deleted successfully.');
    }
}
