<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Allow access only to users with 'admin' or 'super_admin' role
     * within the current event (resolved by EventContextMiddleware).
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $role = $request->get('_event_role');

        if (! $user || ! in_array($role, ['admin', 'super_admin'])) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
