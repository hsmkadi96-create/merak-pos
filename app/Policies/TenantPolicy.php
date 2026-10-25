<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

class TenantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('manage_tenants');
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('super_admin')
            || $user->can('manage_tenants')
            || $user->tenant()->where('tenant_id', $tenant->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('manage_tenants');
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('super_admin') || $user->can('manage_tenants');
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('super_admin') || $user->can('manage_tenants');
    }
}
