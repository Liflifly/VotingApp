<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCandidateController extends Controller
{
    public function index(Election $election)
    {
        $candidates = $election->candidates()->orderBy('order_number')->get();

        return \Inertia\Inertia::render('Admin/Candidates/Index', compact('election', 'candidates'));
    }

    public function create(Election $election)
    {
        return \Inertia\Inertia::render('Admin/Candidates/Create', compact('election'));
    }

    public function store(Request $request, Election $election)
    {
        if ($election->status === 'active') {
            abort(403, 'Waduh, periode pemilihan lagi aktif nih, gak boleh diotak-atik!');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'order_number' => 'nullable|integer|min:1',
            'class' => 'required|string|max:255',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'program' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('candidates', 'public');
        }

        $nextOrder = $election->candidates()->max('order_number');
        $nextOrder = $nextOrder ? $nextOrder + 1 : 1;

        $election->candidates()->create([
            'name' => $request->name,
            'order_number' => $request->order_number ?? $nextOrder,
            'class' => $request->class,
            'vision' => $request->vision,
            'mission' => $request->mission,
            'program' => $request->program,
            'photo' => $photoPath,
        ]);

        return redirect()->route('admin.candidates.index', $election)->with('success', 'Kandidat berhasil bergabung dalam pesta demokrasi!');
    }

    public function edit(Election $election, Candidate $candidate)
    {
        return \Inertia\Inertia::render('Admin/Candidates/Edit', compact('election', 'candidate'));
    }

    public function update(Request $request, Election $election, Candidate $candidate)
    {
        if ($election->status === 'active') {
            abort(403, 'Waduh, periode pemilihan lagi aktif nih, gak boleh diotak-atik!');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'order_number' => 'nullable|integer|min:1',
            'class' => 'required|string|max:255',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'program' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'order_number' => $request->order_number ?: $candidate->order_number,
            'class' => $request->class,
            'vision' => $request->vision,
            'mission' => $request->mission,
            'program' => $request->program,
        ];

        if ($request->hasFile('photo')) {
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }
            $data['photo'] = $request->file('photo')->store('candidates', 'public');
        }

        $candidate->update($data);

        return redirect()->route('admin.candidates.index', $election)->with('success', 'Mantap! Profil kandidat sudah di-update menjadi lebih keren.');
    }

    public function destroy(Election $election, Candidate $candidate)
    {
        if ($election->status === 'active') {
            abort(403, 'Waduh, periode pemilihan lagi aktif nih, gak boleh diotak-atik!');
        }
        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo);
        }

        $candidate->delete();

        return redirect()->route('admin.candidates.index', $election)->with('success', 'Kandidat telah dihapus. Selesai sudah perjalanannya di sini.');
    }
}
