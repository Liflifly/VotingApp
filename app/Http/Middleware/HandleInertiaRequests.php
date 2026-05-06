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
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'nis' => $request->user()->nis,
                    'avatar' => $request->user()->avatar
                        ? '/storage/' . $request->user()->avatar
                        : null,
                    'avatar_original' => $request->user()->avatar_original
                        ? '/storage/' . $request->user()->avatar_original
                        : null,
                    'role' => $request->user()->role,
                    'has_voted' => $request->user()->has_voted ?? false,
                ] : null,
            ],
            'activeElection' => \App\Models\Election::active()->first(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'status' => fn () => $request->session()->get('status'),
            ],
        ];
    }
}
