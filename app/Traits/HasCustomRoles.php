<?php

namespace App\Traits;

use App\Models\Role;

trait HasCustomRoles
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)
            ->where('organization_id', $this->organization_id)
            ->first();
            
        if (!$role) {
            // Check if Super Admin context
            if ($this->organization_id === null) {
                $role = Role::firstOrCreate(['name' => $roleName, 'organization_id' => null]);
            } else {
                throw new \Exception("Role {$roleName} does not exist for this organization.");
            }
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole($roleName)
    {
        $role = Role::where('name', $roleName)
            ->where('organization_id', $this->organization_id)
            ->first();

        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }

    public function hasPermission($permissionName)
    {
        // For super admins or organization admins, we might want to bypass, but let's stick to explicit role permissions.
        // Wait, Super Admin should have all permissions.
        if ($this->hasRole('Super Admin')) {
            return true;
        }

        // Organization Admins have all permissions in their org
        if ($this->hasRole('Organization Admin')) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permissionName)) {
                return true;
            }
        }

        return false;
    }
}
