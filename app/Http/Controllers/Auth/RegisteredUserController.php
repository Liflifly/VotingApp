<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): \Inertia\Response
    {
        return \Inertia\Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // SEC-05 FIX: Validasi format NIS (numerik, 5-20 digit, unik per kolom)
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nis'      => ['required', 'digits_between:5,20', 'unique:users,nis'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // SEC-01 FIX: Gunakan explicit property assignment, bukan User::create()
        // sehingga kolom sensitif (role, has_voted) tidak pernah di-mass assign
        $user = new User();
        $user->name     = $request->name;
        $user->nis      = $request->nis;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->has_voted = false;
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
