<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-based access middleware.
 *
 * Usage in routes:
 *   Route::middleware(['auth', 'tenant', 'role:manager'])
 *   Route::middleware(['auth', 'tenant', 'role:manager,teacher'])  // multiple roles
 *
 * Roles defined on User model:
 *   super_admin, center_manager, teacher, accountant
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // super_admin bypasses all role checks
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
