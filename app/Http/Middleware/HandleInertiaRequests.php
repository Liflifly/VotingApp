<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user      = $request->user();
        
        // Robust detection: try merged data first, fallback to route parameters
        $event     = $request->get('_current_event') ?? $request->route('event');
        $eventRole = $request->get('_event_role');

        // If $event is just a string (slug), resolve it to a model to get the role
        if (is_string($event)) {
            $event = \App\Models\Event::where('slug', $event)->first();
        }

        // If we have an event but no role yet, resolve it manually
        if ($event instanceof \App\Models\Event && $user && ! $eventRole) {
            $eventRole = $event->getUserRole($user);
        }

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user ? [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'email'           => $user->email,
                    'avatar'          => $user->avatar ? '/storage/' . $user->avatar : null,
                    'avatar_original' => $user->avatar_original ? '/storage/' . $user->avatar_original : null,
                    'role'            => $eventRole, 
                ] : null,
            ],

            'currentEvent' => ($event instanceof \App\Models\Event) ? [
                'id'                 => $event->id,
                'name'               => $event->name,
                'slug'               => $event->slug,
                'theme'              => $event->theme,
                'results_visibility' => $event->results_visibility,
            ] : null,

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
                'status'  => fn () => $request->session()->get('status'),
            ],
        ];
    }
}
