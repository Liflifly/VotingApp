<?php

namespace App\Http\Controllers;

use App\Models\Candidate;

class CandidateController extends Controller
{
    public function show(Candidate $candidate)
    {
        $activeElection = \App\Models\Election::active()->first();
        $totalCandidates = $activeElection ? \App\Models\Candidate::where('election_id', $activeElection->id)->count() : 0;
        $user = auth()->user();

        return \Inertia\Inertia::render('Vote/Show', compact('candidate', 'activeElection', 'totalCandidates', 'user'));
    }
}
