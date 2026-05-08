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
        $user     = $request->user();
        $event    = $request->get('_current_event');
        $eventRole = $request->get('_event_role');

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user ? [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'email'           => $user->email,
                    'avatar'          => $user->avatar ? '/storage/' . $user->avatar : null,
                    'avatar_original' => $user->avatar_original ? '/storage/' . $user->avatar_original : null,
                    'role'            => $eventRole,  // Role is event-scoped
                ] : null,
            ],

            'currentEvent' => $event ? [
                'id'    => $event->id,
                'name'  => $event->name,
                'slug'  => $event->slug,
                'theme' => $event->theme,
            ] : null,

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'status'  => fn () => $request->session()->get('status'),
            ],
        ];
    }
}
