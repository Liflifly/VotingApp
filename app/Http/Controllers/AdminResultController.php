<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;
use App\Models\Vote;

class AdminResultController extends Controller
{
    /**
     * BUG FIX: Sebelumnya mengambil semua kandidat & votes tanpa filter election,
     * sehingga hasil dari berbagai periode bercampur dan totalnya salah.
     * Sekarang filter by election yang dipilih (default: active atau latest ended).
     */
    public function index()
    {
        // Ambil semua election untuk dropdown filter
        $elections = Election::latest()->get();

        $selectedElection = Election::where('status', 'active')->first()
            ?? Election::where('status', 'ended')->latest()->first()
            ?? Election::latest()->first();

        $results = collect();
        $totalVotes = 0;
        $totalVoters = User::where('role', 'user')->count();

        if ($selectedElection) {
            $results = Candidate::where('election_id', $selectedElection->id)
                ->withCount('votes')
                ->orderByDesc('votes_count')
                ->get();

            $totalVotes = Vote::where('election_id', $selectedElection->id)->count();
        }

        return \Inertia\Inertia::render('Admin/Results/Index', compact(
            'results',
            'totalVoters',
            'totalVotes',
            'elections',
            'selectedElection'
        ));
    }
}
