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
     * Guests can see the event name and choose to join.
     */
    public function show(Event $event)
    {
        if ($event->status === 'archived') {
            abort(404);
        }

        $activeElection = $event->activeElection();
        $totalVoters    = $event->users()->wherePivot('role', 'voter')->count();

        return Inertia::render('Event/Landing', [
            'event'          => $event->only('id', 'name', 'slug', 'description', 'theme'),
            'activeElection' => $activeElection,
            'totalVoters'    => $totalVoters,
        ]);
    }

    // ─── Create Event ─────────────────────────────────────────────────────────

    /**
     * Show the "Create Event" form.
     * User must be authenticated to create an event.
     */
    public function create()
    {
        return Inertia::render('Event/Create');
    }

    /**
     * Store a new event. The creator automatically becomes super_admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'theme'       => ['required', 'in:neo-brutalism,semi-formal,formal'],
        ]);

        $user  = $request->user();
        $event = Event::create([
            ...$validated,
            'created_by' => $user->id,
        ]);

        // Attach creator as super_admin in the pivot
        $event->users()->attach($user->id, ['role' => 'super_admin']);

        return redirect()->route('events.dashboard', $event)
            ->with('success', 'Event created! You are now the Super Admin.');
    }

    // ─── Join Event ───────────────────────────────────────────────────────────

    /**
     * Show the "Join Event" page (role selection: admin vs voter).
     */
    public function join(Event $event)
    {
        if ($event->status === 'archived') {
            abort(404);
        }

        // If already a member, redirect to dashboard
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
     * Join as an admin using an invite token.
     * If user is not authenticated, they register first then are attached.
     */
    public function joinAsAdmin(Request $request, Event $event)
    {
        $request->validate([
            'token'    => ['required', 'string'],
            'name'     => ['required_without:_authenticated', 'string', 'max:255'],
            'email'    => ['required_without:_authenticated', 'email', 'unique:users,email'],
            'password' => ['required_without:_authenticated', 'confirmed', Rules\Password::defaults()],
        ]);

        // Validate the invite token
        $invite = $event->inviteTokens()
            ->valid()
            ->where('token', $request->token)
            ->first();

        if (! $invite) {
            return back()->withErrors(['token' => 'Invalid or expired invite token.']);
        }

        $user = Auth::user();

        if (! $user) {
            // Register new user
            $user = new User();
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            Auth::login($user);
        }

        // Check not already a member
        if ($event->getUserRole($user) !== null) {
            return redirect()->route('events.dashboard', $event)
                ->with('error', 'You are already a member of this event.');
        }

        // Attach with the role from the token
        $event->users()->attach($user->id, ['role' => $invite->role]);
        $invite->markUsed($user);

        return redirect()->route('events.dashboard', $event)
            ->with('success', 'Welcome! You joined as ' . ucfirst(str_replace('_', ' ', $invite->role)) . '.');
    }

    /**
     * Register as a voter for an event (with optional dynamic fields).
     */
    public function joinAsVoter(Request $request, Event $event)
    {
        $baseRules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Dynamically add validation for custom voter fields
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

        $validated = $request->validate(array_merge($baseRules, $dynamicRules));

        $user = new User();
        $user->name     = $validated['name'];
        $user->email    = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();

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

        Auth::login($user);

        return redirect()->route('events.dashboard', $event)
            ->with('success', 'Welcome! You are now registered as a voter.');
    }
}
