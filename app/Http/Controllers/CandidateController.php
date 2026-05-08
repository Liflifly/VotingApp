<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CandidateController extends Controller
{
    public function show(Event $event, Candidate $candidate)
    {
        $activeElection  = $event->activeElection();
        $candidateFields = $event->candidateFieldDefinitions()->get()->map->toFormField();
        $totalCandidates = $activeElection
            ? Candidate::where('election_id', $activeElection->id)->count()
            : 0;

        return Inertia::render('Vote/Show', compact(
            'event',
            'candidate',
            'activeElection',
            'totalCandidates',
            'candidateFields',
        ));
    }
}
