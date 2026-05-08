<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Event;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminElectionController extends Controller
{
    public function index(Event $event)
    {
        $elections = $event->elections()->latest()->get();

        return Inertia::render('Admin/Elections/Index', compact('event', 'elections'));
    }

    public function create(Event $event)
    {
        return Inertia::render('Admin/Elections/Create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at'   => ['required', 'date', 'after:starts_at'],
            'notes'     => ['nullable', 'string'],
        ]);

        $event->elections()->create($validated);

        return redirect()->route('events.admin.elections.index', $event)
            ->with('success', 'Election period created.');
    }

    public function show(Event $event, Election $election)
    {
        $election->load('candidates');
        $totalVoters = $event->users()->wherePivot('role', 'voter')->count();

        return Inertia::render('Admin/Elections/Show', compact('event', 'election', 'totalVoters'));
    }

    public function edit(Event $event, Election $election)
    {
        if (! in_array($election->status, ['draft', 'active'])) {
            return redirect()->route('events.admin.elections.show', [$event, $election])
                ->with('error', 'Only Draft or Active elections can be edited.');
        }

        return Inertia::render('Admin/Elections/Edit', compact('event', 'election'));
    }

    public function update(Request $request, Event $event, Election $election)
    {
        if (! in_array($election->status, ['draft', 'active'])) {
            return redirect()->route('events.admin.elections.show', [$event, $election])
                ->with('error', 'Only Draft or Active elections can be edited.');
        }

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at'   => ['required', 'date', 'after:starts_at'],
            'notes'     => ['nullable', 'string'],
        ]);

        $election->update($validated);

        return redirect()->route('events.admin.elections.index', $event)
            ->with('success', 'Election updated.');
    }

    public function destroy(Event $event, Election $election)
    {
        if ($election->status !== 'draft') {
            return back()->with('error', 'Only Draft elections can be deleted.');
        }

        $election->delete();

        return redirect()->route('events.admin.elections.index', $event)
            ->with('success', 'Election deleted.');
    }

    public function activate(Request $request, Event $event, Election $election)
    {
        if ($election->starts_at && now()->lt($election->starts_at)) {
            return back()->with('error', 'Election start time has not been reached yet.');
        }

        if ($election->candidates()->count() < 2) {
            return back()->with('error', 'At least 2 candidates are required to start an election.');
        }

        DB::transaction(function () use ($event, $election) {
            // End any other active elections in this event
            $event->elections()->where('id', '!=', $election->id)->update(['status' => 'ended']);
            $election->update(['status' => 'active']);
        });

        return redirect()->route('events.admin.elections.index', $event)
            ->with('success', 'Election is now active!');
    }

    public function end(Event $event, Election $election)
    {
        DB::transaction(function () use ($event, $election) {
            $totalVoters = $event->users()->wherePivot('role', 'voter')->count();
            $election->update([
                'status'       => 'ended',
                'total_voters' => $totalVoters,
            ]);
        });

        return redirect()->route('events.admin.elections.index', $event)
            ->with('success', 'Election ended. Results are now final.');
    }

    public function history(Event $event)
    {
        $elections = $event->elections()->where('status', 'ended')->latest()->get();

        return Inertia::render('Admin/Elections/History', compact('event', 'elections'));
    }
}
