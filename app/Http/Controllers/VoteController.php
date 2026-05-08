<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Event;
use App\Models\Vote;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VoteController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $user           = Auth::user();
        $activeElection = $event->activeElection();

        if (! $activeElection) {
            return Inertia::render('Vote/Index', [
                'activeElection'  => null,
                'candidates'      => [],
                'totalVotes'      => 0,
                'hasVoted'        => false,
            ]);
        }

        $candidateFields = $event->candidateFieldDefinitions()->get()->map->toFormField();
        $candidates      = Candidate::where('election_id', $activeElection->id)
            ->withCount('votes')
            ->orderBy('order_number')
            ->get()
            ->map(fn ($c) => [
                'id'           => $c->id,
                'order_number' => $c->order_number,
                'fields'       => $c->fields,
                'photo_url'    => $c->photo_url,
                'votes_count'  => $c->votes_count,
            ]);

        $totalVotes = Vote::where('election_id', $activeElection->id)->count();
        $hasVoted   = $user->hasVotedInElection($activeElection);

        return Inertia::render('Vote/Index', [
            'activeElection'  => $activeElection,
            'candidates'      => $candidates,
            'candidateFields' => $candidateFields,
            'totalVotes'      => $totalVotes,
            'hasVoted'        => $hasVoted,
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $user = Auth::user();

        // Only voters can cast votes
        if (! in_array($request->get('_event_role'), ['voter'])) {
            return back()->with('error', 'Only registered voters can cast votes.');
        }

        return DB::transaction(function () use ($request, $user, $event) {
            $data = $request->validate([
                'candidate_id' => ['required', 'exists:candidates,id'],
            ]);

            $activeElection = $event->elections()
                ->active()
                ->lockForUpdate()
                ->first();

            if (! $activeElection) {
                return back()->with('error', 'No active election at this time.');
            }

            // Check for double-vote (fresh from DB)
            if ($user->hasVotedInElection($activeElection)) {
                return back()->with('error', 'You have already cast your vote in this election.');
            }

            // Ensure candidate belongs to this election
            $candidate = Candidate::where('id', $data['candidate_id'])
                ->where('election_id', $activeElection->id)
                ->first();

            if (! $candidate) {
                return back()->with('error', 'Invalid candidate for this election.');
            }

            try {
                // DB unique constraint on (user_id, election_id) catches race conditions
                Vote::create([
                    'user_id'      => $user->id,
                    'event_id'     => $event->id,
                    'election_id'  => $activeElection->id,
                    'candidate_id' => $data['candidate_id'],
                ]);
            } catch (UniqueConstraintViolationException) {
                return back()->with('error', 'Your vote was already recorded.');
            }

            return redirect()->route('events.results', $event)
                ->with('success', 'Your vote has been recorded!');
        });
    }

    public function results(Request $request, Event $event)
    {
        $userRole = $request->get('_event_role');

        // Enforce results visibility for voters
        if ($userRole === 'voter' && ! $event->isResultsPublic()) {
            return Inertia::render('Results/Index', [
                'candidates'      => [],
                'totalVotes'      => 0,
                'election'        => null,
                'candidateFields' => [],
                'accessDenied'    => true,
            ]);
        }

        $election = $event->elections()->active()->first()
            ?? $event->elections()->where('status', 'ended')->latest()->first();

        if (! $election) {
            return Inertia::render('Results/Index', [
                'candidates'      => [],
                'totalVotes'      => 0,
                'election'        => null,
                'candidateFields' => [],
                'accessDenied'    => false,
            ]);
        }

        $candidateFields = $event->candidateFieldDefinitions()->get()->map->toFormField();
        $candidates      = Candidate::where('election_id', $election->id)
            ->withCount('votes')
            ->orderBy('order_number')
            ->get()
            ->map(fn ($c) => [
                'id'           => $c->id,
                'order_number' => $c->order_number,
                'fields'       => $c->fields,
                'photo_url'    => $c->photo_url,
                'votes_count'  => $c->votes_count,
            ]);

        $totalVotes = Vote::where('election_id', $election->id)->count();

        return Inertia::render('Results/Index', [
            'candidates'      => $candidates,
            'totalVotes'      => $totalVotes,
            'election'        => $election,
            'candidateFields' => $candidateFields,
            'accessDenied'    => false,
        ]);
    }
}
