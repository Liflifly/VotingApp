<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Event;
use App\Models\Vote;
use Inertia\Inertia;

class AdminResultController extends Controller
{
    public function index(Event $event)
    {
        $elections = $event->elections()->latest()->get();

        // Support switching election via query param
        $selectedElection = null;
        if (request()->has('election_id')) {
            $selectedElection = $event->elections()->find(request()->integer('election_id'));
        }

        $selectedElection ??= $event->elections()->where('status', 'active')->first()
            ?? $event->elections()->where('status', 'ended')->latest()->first()
            ?? $event->elections()->latest()->first();

        $results         = collect();
        $totalVotes      = 0;
        $totalVoters     = $event->users()->wherePivot('role', 'voter')->count();
        $candidateFields = collect();

        if ($selectedElection) {
            $candidateFields = $event->candidateFieldDefinitions()->get()->map->toFormField();
            $results         = Candidate::where('election_id', $selectedElection->id)
                ->withCount('votes')
                ->orderByDesc('votes_count')
                ->get()
                ->map(fn ($c) => [
                    'id'           => $c->id,
                    'order_number' => $c->order_number,
                    'fields'       => $c->fields,
                    'photo_url'    => $c->photo_url,
                    'votes_count'  => $c->votes_count,
                ]);

            $totalVotes = Vote::where('election_id', $selectedElection->id)->count();
        }

        return Inertia::render('Admin/Results/Index', compact(
            'event',
            'results',
            'totalVoters',
            'totalVotes',
            'elections',
            'selectedElection',
            'candidateFields',
        ));
    }
}
