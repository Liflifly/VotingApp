<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminElectionController extends Controller
{
    public function index()
    {
        $elections = Election::latest()->get();
        return \Inertia\Inertia::render('Admin/Elections/Index', compact('elections'));
    }

    public function create()
    {
        return \Inertia\Inertia::render('Admin/Elections/Create');
    }

    /**
     * BUG FIX: Sebelumnya pakai $request->all() → rawan mass assignment attack.
     * Sekarang hanya ambil field yang sudah divalidasi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at'   => 'required|date|after:starts_at',
            'notes'     => 'nullable|string',
        ]);

        Election::create($validated);

        return redirect()->route('admin.elections.index')
                         ->with('success', 'Yuhu! Periode pemilihan baru siap meramaikan suasana.');
    }

    public function show(Election $election)
    {
        $election->load('candidates.votes');
        $totalVoters = User::where('role', 'user')->count();

        return \Inertia\Inertia::render('Admin/Elections/Show', compact('election', 'totalVoters'));
    }

    public function edit(Election $election = null)
    {
        if (!$election) {
            $election = Election::query()->latest('id')->first();
            return \Inertia\Inertia::render('Admin/Election/Edit', compact('election'));
        }

        if ($election->status !== 'draft') {
            return redirect()->route('admin.elections.show', $election)
                             ->with('error', 'Waduh, cuma periode yang masih status Draft yang boleh dimodif!');
        }

        return \Inertia\Inertia::render('Admin/Elections/Edit', compact('election'));
    }

    /**
     * BUG FIX: Sama seperti store(), sebelumnya update juga pakai $request->all().
     */
    public function update(Request $request, Election $election = null)
    {
        // Backward compatibility: route lama tanpa election ID
        if (!$election) {
            $data = $request->validate([
                'name'      => ['nullable', 'string', 'max:255'],
                'starts_at' => ['nullable', 'date'],
                'ends_at'   => ['nullable', 'date', 'after:starts_at'],
            ]);

            if (empty($data['name'])) {
                $data['name'] = 'Pemilihan Umum';
            }

            $election = Election::query()->latest('id')->first();
            if (!$election) {
                $election = Election::create($data);
            } else {
                $election->update($data);
            }

            return redirect()->route('admin.election.edit')
                             ->with('success', 'Beres! Waktu pemilihan sudah disetel ulang dengan mantap.');
        }

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at'   => 'required|date|after:starts_at',
            'notes'     => 'nullable|string',
        ]);

        if ($election->status !== 'draft') {
            return redirect()->route('admin.elections.show', $election)
                             ->with('error', 'Waduh, cuma periode yang masih status Draft yang boleh dimodif!');
        }

        $election->update($validated);

        return redirect()->route('admin.elections.index')
                         ->with('success', 'Mantap! Info periode sudah di-update secara optimal.');
    }

    public function activate(Election $election)
    {
        DB::transaction(function () use ($election) {
            Election::where('id', '!=', $election->id)->update(['status' => 'ended']);
            $election->update(['status' => 'active']);
            User::query()->update(['has_voted' => false, 'voted_election_id' => null]);
        });

        return redirect()->route('admin.elections.index')
                         ->with('success', 'Gas! Pemilihan resmi dimulai. Let the best win!');
    }

    public function end(Election $election)
    {
        DB::transaction(function () use ($election) {
            $election->update([
                'status'        => 'ended',
                'total_voters'  => User::where('role', 'user')->count(),
            ]);
        });

        return redirect()->route('admin.elections.index')
                         ->with('success', 'Dan... cut! Waktu pemilihan ini resmi ditutup.');
    }

    public function history()
    {
        $elections = Election::where('status', 'ended')->latest()->get();
        return \Inertia\Inertia::render('Admin/Elections/History', compact('elections'));
    }
}