<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureTenantIsSet
 *
 * Protects ALL authenticated routes. Blocks any user without a tenant unless
 * they are a Super Admin. This middleware prevents accidental cross-tenant access.
 */
class EnsureTenantIsSet
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow super_admin without a tenant_id (they manage all tenants)
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // All other roles must have a tenant_id set
        if (!$user || !$user->tenant_id) {
            abort(403, 'Tài khoản chưa được gán Trung tâm. Vui lòng liên hệ quản trị viên.');
        }

        return $next($request);
    }
}
