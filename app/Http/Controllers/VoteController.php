<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Election;

class VoteController extends Controller
{
    public function index()
    {
        $user            = Auth::user();
        $activeElection  = Election::active()->first();

        if (!$activeElection) {
            return \Inertia\Inertia::render('Vote/Index', [
                'user'            => $user,
                'activeElection'  => null,
            ]);
        }

        $candidates = Candidate::where('election_id', $activeElection->id)
                                ->withCount('votes')
                                ->orderBy('order_number')
                                ->get();

        $totalVotes = Vote::where('election_id', $activeElection->id)->count();

        return \Inertia\Inertia::render('Vote/Index', [
            'candidates'     => $candidates,
            'user'           => $user,
            'activeElection' => $activeElection,
            'totalVotes'     => $totalVotes,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
        ]);

        $activeElection = Election::active()->first();

        if (!$activeElection) {
            return back()->with('error', 'Waduh, belum ada panggung pemilihan yang buka, nih.');
        }

        if (in_array($user->role, ['admin', 'super_admin'])) {
            return back()->with('error', 'Maaf, Anda tidak dapat melakukan vote karena bukan akun siswa.');
        }

        if ($user->hasVotedInElection($activeElection)) {
            return back()->with('error', 'Ups! Kamu sudah nyoblos, masa mau dua kali?');
        }

        // Pastikan kandidat memang milik election yang aktif
        $candidate = Candidate::where('id', $data['candidate_id'])
                               ->where('election_id', $activeElection->id)
                               ->first();

        if (!$candidate) {
            return back()->with('error', 'Eh, kandidat ini nyasar atau bukan dari periode ini.');
        }

        DB::transaction(function () use ($user, $data, $activeElection) {
            Vote::create([
                'user_id'      => $user->id,
                'candidate_id' => $data['candidate_id'],
                'election_id'  => $activeElection->id,
            ]);
            $user->update([
                'has_voted'         => true,
                'voted_election_id' => $activeElection->id,
            ]);
        });

        return redirect()->route('results.index')->with('success', 'Keren! Suaramu udah aman tercatat di kotak suara.');
    }

    /**
     * BUG FIX: Sebelumnya results() tidak filter by election,
     * sehingga menampilkan semua suara dari semua election dicampur.
     * Sekarang filter by active election (atau election terakhir jika sudah selesai).
     */
    public function results()
    {
        // Coba ambil election aktif dulu, kalau tidak ada ambil yang terakhir (ended)
        $election = Election::active()->first()
                ?? Election::where('status', 'ended')->latest()->first();

        if (!$election) {
            return \Inertia\Inertia::render('Results/Index', [
                'candidates'  => collect(),
                'totalVotes'  => 0,
                'election'    => null,
            ]);
        }

        $candidates = Candidate::where('election_id', $election->id)
                                ->withCount('votes')
                                ->orderBy('order_number')
                                ->get();

        $totalVotes = Vote::where('election_id', $election->id)->count();

        return \Inertia\Inertia::render('Results/Index', [
            'candidates'  => $candidates,
            'totalVotes'  => $totalVotes,
            'election'    => $election,
        ]);
    }
}