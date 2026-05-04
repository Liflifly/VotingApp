<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeElection = Election::active()->first();

        if (! $activeElection) {
            return \Inertia\Inertia::render('Vote/Index', [
                'user'           => $user,
                'activeElection' => null,
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

        return DB::transaction(function () use ($request, $user) {
            $data = $request->validate([
                'candidate_id' => ['required', 'exists:candidates,id'],
            ]);

            $activeElection = Election::active()->lockForUpdate()->first();

            if (! $activeElection) {
                return back()->with('error', 'Waduh, belum ada panggung pemilihan yang buka, nih.');
            }

            if (in_array($user->role, ['admin', 'super_admin'])) {
                return back()->with('error', 'Maaf, Anda tidak dapat melakukan vote karena bukan akun siswa.');
            }

            // BUG-02 FIX: Fresh check dari DB + query ke tabel votes (source of truth)
            $user->refresh();
            if ($user->hasVotedInElection($activeElection)) {
                return back()->with('error', 'Ups! Kamu sudah nyoblos, masa mau dua kali?');
            }

            // Pastikan kandidat memang milik election yang aktif
            $candidate = Candidate::where('id', $data['candidate_id'])
                ->where('election_id', $activeElection->id)
                ->first();

            if (! $candidate) {
                return back()->with('error', 'Eh, kandidat ini nyasar atau bukan dari periode ini.');
            }

            try {
                // BUG-01 FIX: Tangkap UniqueConstraintViolationException dari race condition.
                // DB unique constraint (user_id, election_id) berfungsi sebagai safety net terakhir.
                Vote::create([
                    'user_id'      => $user->id,
                    'candidate_id' => $data['candidate_id'],
                    'election_id'  => $activeElection->id,
                ]);

                // SEC-01 FIX: Update langsung via explicit assignment, bukan via fillable
                $user->has_voted        = true;
                $user->voted_election_id = $activeElection->id;
                $user->save();
            } catch (UniqueConstraintViolationException $e) {
                // Race condition tertangkap — user berhasil submit dua request serentak
                return back()->with('error', 'Ups! Suaramu sudah tercatat. Tidak bisa memilih dua kali.');
            }

            return redirect()->route('results.index')->with('success', 'Keren! Suaramu udah aman tercatat di kotak suara.');
        });
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

        if (! $election) {
            return \Inertia\Inertia::render('Results/Index', [
                'candidates' => collect(),
                'totalVotes' => 0,
                'election'   => null,
            ]);
        }

        $candidates = Candidate::where('election_id', $election->id)
            ->withCount('votes')
            ->orderBy('order_number')
            ->get();

        $totalVotes = Vote::where('election_id', $election->id)->count();

        return \Inertia\Inertia::render('Results/Index', [
            'candidates' => $candidates,
            'totalVotes' => $totalVotes,
            'election'   => $election,
        ]);
    }
}
