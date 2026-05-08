<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;

class EventContextMiddleware
{
    /**
     * Resolve the current event from the route parameter and make it
     * available throughout the request lifecycle via the request object.
     *
     * Also validates the authenticated user is a member of this event.
     */
    public function handle(Request $request, Closure $next)
    {
        $event = $request->route('event');

        if (! $event instanceof Event) {
            abort(404, 'Event not found.');
        }

        if ($event->status === 'archived') {
            abort(403, 'This event has been archived.');
        }

        // Validate the authenticated user belongs to this event
        $user = $request->user();
        if ($user) {
            $role = $event->getUserRole($user);
            if ($role === null) {
                abort(403, 'You are not a member of this event.');
            }
            // Cache the role on the request for downstream use
            $request->merge(['_event_role' => $role]);
        }

        // Make event available via request
        $request->merge(['_current_event' => $event]);

        return $next($request);
    }
}
