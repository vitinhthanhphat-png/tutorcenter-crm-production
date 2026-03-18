<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * BelongsToTenant
 *
 * Upgraded to support multi-tenant access:
 * - Super Admin: no scope (sees all tenants)
 * - Users with assignments: sees home tenant + all assigned tenants
 * - Regular users: sees only their home tenant
 *
 * SECURITY: This is the core multi-tenant isolation mechanism.
 * If a model is missing this trait, it WILL expose cross-tenant data.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (!Auth::check()) {
                return;
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Super Admin: no restriction
            if ($user->isSuperAdmin()) {
                return;
            }

            // Get all accessible tenant IDs (home + assignments)
            $tenantIds = $user->accessibleTenantIds();

            if (empty($tenantIds)) {
                // User has no tenant at all — show nothing
                $builder->whereRaw('1 = 0');
                return;
            }

            // Scope to all accessible tenants
            $builder->whereIn(
                (new static)->qualifyColumn('tenant_id'),
                $tenantIds
            );
        });

        // Auto-fill tenant_id on creation (use home tenant)
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id && empty($model->tenant_id)) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }

    /**
     * Temporarily disable the tenant scope for a single query.
     * Use only in Super Admin context.
     */
    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope('tenant');
    }

    /**
     * Scope to a specific tenant only (bypass multi-tenant expansion).
     */
    public static function forTenant(int $tenantId): Builder
    {
        return static::withoutGlobalScope('tenant')
                     ->where((new static)->qualifyColumn('tenant_id'), $tenantId);
    }
}
