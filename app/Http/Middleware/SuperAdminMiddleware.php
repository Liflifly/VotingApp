<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    /**
     * Allow access only to users with 'super_admin' role
     * within the current event (resolved by EventContextMiddleware).
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $role = $request->get('_event_role');

        if (! $user || $role !== 'super_admin') {
            abort(403, 'Super Admin access required.');
        }

        return $next($request);
    }
}
