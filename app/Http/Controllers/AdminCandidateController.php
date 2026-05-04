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

        // SEC-03 FIX: Gunakan return value dari validate() sebagai sumber data
        // SEC-04 FIX: Hapus 'gif', tambahkan validasi dimensions minimum
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'order_number' => 'nullable|integer|min:1',
            'class'        => 'required|string|max:255',
            'vision'       => 'required|string',
            'mission'      => 'required|string',
            'program'      => 'nullable|string',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('candidates', 'public');
        }

        $nextOrder = $election->candidates()->max('order_number');
        $nextOrder = $nextOrder ? $nextOrder + 1 : 1;

        // SEC-03 FIX: Data diambil dari $validated, bukan langsung dari $request->property
        $election->candidates()->create([
            'name'         => $validated['name'],
            'order_number' => $validated['order_number'] ?? $nextOrder,
            'class'        => $validated['class'],
            'vision'       => $validated['vision'],
            'mission'      => $validated['mission'],
            'program'      => $validated['program'] ?? null,
            'photo'        => $photoPath,
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

        // SEC-03 FIX: Gunakan return value dari validate()
        // SEC-04 FIX: Hapus 'gif', tambahkan validasi dimensions minimum
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'order_number' => 'nullable|integer|min:1',
            'class'        => 'required|string|max:255',
            'vision'       => 'required|string',
            'mission'      => 'required|string',
            'program'      => 'nullable|string',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
        ]);

        $data = [
            'name'         => $validated['name'],
            'order_number' => $validated['order_number'] ?: $candidate->order_number,
            'class'        => $validated['class'],
            'vision'       => $validated['vision'],
            'mission'      => $validated['mission'],
            'program'      => $validated['program'] ?? null,
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
