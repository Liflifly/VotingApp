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
            'image'          => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'original_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'],
        ]);

        $user = $request->user();

        // Delete old avatar from disk if one exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store the new cropped file → avatars/{userId}_{timestamp}.{ext}
        $extension = $request->file('image')->extension() ?: 'jpg';
        $timestamp = time();
        $filename  = $user->id . '_' . $timestamp . '.' . $extension;
        $path      = $request->file('image')->storeAs('avatars', $filename, 'public');

        $user->avatar = $path;

        // Store the original uncropped file if provided
        if ($request->hasFile('original_image')) {
            if ($user->avatar_original && Storage::disk('public')->exists($user->avatar_original)) {
                Storage::disk('public')->delete($user->avatar_original);
            }

            $origExtension = $request->file('original_image')->extension() ?: 'jpg';
            $origFilename  = $user->id . '_' . $timestamp . '_original.' . $origExtension;
            $origPath      = $request->file('original_image')->storeAs('avatars', $origFilename, 'public');
            
            $user->avatar_original = $origPath;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Delete the user's avatar photo.
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        }

        if ($user->avatar_original) {
            if (Storage::disk('public')->exists($user->avatar_original)) {
                Storage::disk('public')->delete($user->avatar_original);
            }
            $user->avatar_original = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'avatar-deleted');
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