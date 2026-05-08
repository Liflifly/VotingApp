<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInviteToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class EventController extends Controller
{
    // ─── Public Pages ─────────────────────────────────────────────────────────

    /**
     * Show the landing page for a specific event (public).
     */
    public function show(Event $event)
    {
        if ($event->status === 'archived') {
            abort(404);
        }

        $activeElection = $event->activeElection();
        $totalVoters    = $event->users()->wherePivot('role', 'voter')->count();

        return Inertia::render('Event/Join', [
            'event'          => $event->only('id', 'name', 'slug', 'description', 'theme'),
            'activeElection' => $activeElection,
            'totalVoters'    => $totalVoters,
        ]);
    }

    // ─── Create Event ─────────────────────────────────────────────────────────

    /**
     * Show the "Create Event" form. Requires authentication.
     */
    public function create()
    {
        return Inertia::render('Event/Create');
    }

    /**
     * Store a new event. Creator automatically becomes super_admin.
     * Redirects to Event Settings so they can immediately get their share links.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'theme'              => ['required', 'in:neo-brutalism,semi-formal,formal'],
            'results_visibility' => ['required', 'in:public,private'],
        ]);

        $user  = $request->user();
        $event = Event::create([
            ...$validated,
            'created_by' => $user->id,
        ]);

        // Attach creator as super_admin in the pivot
        $event->users()->attach($user->id, ['role' => 'super_admin']);

        // Redirect to settings page so they can configure fields and get share links
        return redirect()->route('events.admin.settings', $event)
            ->with('success', 'Event created! Configure your voter fields and share the links below.');
    }

    // ─── Join via Access Token Links (Phase 4) ────────────────────────────────

    /**
     * Voter join page — reached via /join/v/{voter_access_token}
     * Requires web login first (redirected back here after login).
     */
    public function joinViaVoterToken(Request $request, string $token)
    {
        $event = Event::where('voter_access_token', $token)->firstOrFail();

        if ($event->status === 'archived') {
            abort(404);
        }

        $user = $request->user();

        // If already a member → redirect to event dashboard
        if ($user && $event->getUserRole($user) !== null) {
            return redirect()->route('events.dashboard', $event);
        }

        $voterFields = $event->voterFieldDefinitions()->get()->map->toFormField();

        return Inertia::render('Event/JoinVoter', [
            'event'            => $event->only('id', 'name', 'slug', 'description', 'theme'),
            'voterFields'      => $voterFields,
            'voterAccessToken' => $token,
        ]);
    }

    /**
     * Admin join page — reached via /join/a/{admin_access_token}
     * Requires web login first. Then user enters invite token.
     */
    public function joinViaAdminToken(Request $request, string $token)
    {
        $event = Event::where('admin_access_token', $token)->firstOrFail();

        if ($event->status === 'archived') {
            abort(404);
        }

        $user = $request->user();

        // If already a member → redirect to event dashboard
        if ($user && $event->getUserRole($user) !== null) {
            return redirect()->route('events.dashboard', $event);
        }

        return Inertia::render('Event/JoinAdmin', [
            'event'            => $event->only('id', 'name', 'slug', 'description', 'theme'),
            'adminAccessToken' => $token,
        ]);
    }

    /**
     * Process voter event registration (custom fields form submission).
     * The user is already authenticated (web account exists).
     */
    public function registerAsVoter(Request $request, string $token)
    {
        $event = Event::where('voter_access_token', $token)->firstOrFail();

        if ($event->status === 'archived') {
            abort(404);
        }

        $user = $request->user();

        // Guard: already a member
        if ($event->getUserRole($user) !== null) {
            return redirect()->route('events.dashboard', $event)
                ->with('info', 'You are already a member of this event.');
        }

        // Build dynamic validation rules from event field definitions
        $fieldDefs    = $event->voterFieldDefinitions()->get();
        $dynamicRules = [];
        foreach ($fieldDefs as $field) {
            $rules = $field->required ? ['required'] : ['nullable'];
            $dynamicRules["fields.{$field->key}"] = match ($field->type) {
                'image'  => [...$rules, 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
                'email'  => [...$rules, 'email'],
                'number' => [...$rules, 'numeric'],
                default  => [...$rules, 'string', 'max:1000'],
            };
        }

        $validated = $request->validate($dynamicRules);

        // Process dynamic field data (handle image uploads)
        $metadata = [];
        foreach ($fieldDefs as $field) {
            if ($field->type === 'image' && $request->hasFile("fields.{$field->key}")) {
                $metadata[$field->key] = $request->file("fields.{$field->key}")
                    ->store('voter-files', 'public');
            } else {
                $metadata[$field->key] = $validated['fields'][$field->key] ?? null;
            }
        }

        $event->users()->attach($user->id, [
            'role'     => 'voter',
            'metadata' => json_encode($metadata),
        ]);

        return redirect()->route('events.dashboard', $event)
            ->with('success', 'Welcome! You are now registered as a voter for ' . $event->name . '.');
    }

    /**
     * Process admin event join via invite token.
     * The user is already authenticated (web account exists).
     */
    public function registerAsAdmin(Request $request, string $token)
    {
        $event = Event::where('admin_access_token', $token)->firstOrFail();

        if ($event->status === 'archived') {
            abort(404);
        }

        $user = $request->user();

        // Guard: already a member
        if ($event->getUserRole($user) !== null) {
            return redirect()->route('events.dashboard', $event)
                ->with('info', 'You are already a member of this event.');
        }

        $request->validate([
            'token' => ['required', 'string'],
        ]);

        // Validate the invite token
        $invite = $event->inviteTokens()
            ->valid()
            ->where('token', $request->token)
            ->first();

        if (! $invite) {
            return back()->withErrors(['token' => 'Invalid or expired invite token.']);
        }

        $event->users()->attach($user->id, ['role' => $invite->role]);
        $invite->markUsed($user);

        return redirect()->route('events.dashboard', $event)
            ->with('success', 'Welcome! You joined as ' . ucfirst(str_replace('_', ' ', $invite->role)) . '.');
    }

    // ─── Legacy join (kept for backward compat, slug-based) ──────────────────

    /**
     * @deprecated Use joinViaVoterToken / joinViaAdminToken instead.
     */
    public function join(Event $event)
    {
        if ($event->status === 'archived') {
            abort(404);
        }

        if (Auth::check() && $event->getUserRole(Auth::user()) !== null) {
            return redirect()->route('events.dashboard', $event);
        }

        $voterFields = $event->voterFieldDefinitions()->get()->map->toFormField();

        return Inertia::render('Event/Join', [
            'event'       => $event->only('id', 'name', 'slug', 'description', 'theme'),
            'voterFields' => $voterFields,
        ]);
    }

    /**
     * @deprecated Use registerAsVoter instead.
     */
    public function joinAsVoter(Request $request, Event $event)
    {
        return redirect()->route('events.show', $event);
    }

    /**
     * @deprecated Use registerAsAdmin instead.
     */
    public function joinAsAdmin(Request $request, Event $event)
    {
        return redirect()->route('events.show', $event);
    }
}
