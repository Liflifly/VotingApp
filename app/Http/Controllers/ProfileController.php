<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => false,
            'status'          => session('status'),
        ]);
    }

    /**
     * Update the user's profile information (name & email).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar photo.
     *
     * Receives a cropped JPEG blob sent via FormData from the NeoCropper modal.
     * Route: POST /profile/avatar → profile.avatar.update
     *
     * Saves to storage/app/public/avatars/ and updates the `avatar`
     * column in the users table.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $user = $request->user();

        // Delete old avatar from disk if one exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store the new file → avatars/{userId}_{timestamp}.{ext}
        $extension = $request->file('image')->extension() ?: 'jpg';
        $filename  = $user->id . '_' . time() . '.' . $extension;
        $path      = $request->file('image')->storeAs('avatars', $filename, 'public');

        // Persist to DB — column is `avatar`, NOT `photo`
        $user->avatar = $path;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}