<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Redirect to installer if app is not installed yet.
 * After installation, a file `storage/installed` is created.
 */
class CheckInstalled
{
    public function handle(Request $request, Closure $next)
    {
        // If the app is NOT installed and user is NOT on install routes → redirect to installer
        if (! file_exists(storage_path('installed')) && ! $request->is('install*')) {
            return redirect()->route('install.index');
        }

        // If the app IS installed and user tries to access install routes → redirect to home
        if (file_exists(storage_path('installed')) && $request->is('install*')) {
            return redirect('/');
        }

        return $next($request);
    }
}
