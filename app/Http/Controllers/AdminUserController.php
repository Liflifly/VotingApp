<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index(Event $event)
    {
        $members = $event->users()
            ->orderBy('name')
            ->get()
            ->map(fn ($user) => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar ? '/storage/' . $user->avatar : null,
                'role'   => $user->pivot->role,
            ]);

        return Inertia::render('Admin/Users/Index', compact('event', 'members'));
    }

    /**
     * Update a member's role within this event.
     * Super admins cannot be demoted through this endpoint.
     */
    public function updateRole(Request $request, Event $event, User $user)
    {
        // Prevent self-modification
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $currentRole = $event->getUserRole($user);

        // Prevent downgrading super_admin
        if ($currentRole === 'super_admin') {
            return back()->with('error', 'Super Admin role cannot be changed here.');
        }

        $data = $request->validate([
            'role' => ['required', 'in:voter,admin'],
        ]);

        $event->users()->updateExistingPivot($user->id, ['role' => $data['role']]);

        return redirect()->route('events.admin.users.index', $event)
            ->with('success', 'Member role updated.');
    }

    /**
     * Remove a member from the event.
     */
    public function destroy(Request $request, Event $event, User $user)
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'You cannot remove yourself from an event.');
        }

        if ($event->getUserRole($user) === 'super_admin') {
            return back()->with('error', 'The Super Admin cannot be removed.');
        }

        $event->users()->detach($user->id);

        return redirect()->route('events.admin.users.index', $event)
            ->with('success', 'Member removed from event.');
    }
}
