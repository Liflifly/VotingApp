<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();

        return \Inertia\Inertia::render('Admin/Users/Index', compact('users'));
    }

    /**
     * SEC-02 FIX:
     * 1. Cegah self-modification (admin mengubah role dirinya sendiri).
     * 2. Cegah Super Admin di-downgrade melalui endpoint ini.
     * 3. Pastikan 'super_admin' tidak bisa di-set melalui endpoint ini.
     */
    public function updateRole(Request $request, User $user)
    {
        // Cegah modifikasi diri sendiri
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Tidak diperbolehkan mengubah role akun sendiri.');
        }

        // Cegah downgrade akun Super Admin
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Akun Super Admin tidak bisa diubah dari sini. Hubungi developer.');
        }

        $data = $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        // SEC-01 FIX: Gunakan explicit assignment, bukan mass assign via fillable
        $user->role = $data['role'];
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Boom! Pangkat admin berhasil diubah sesuai perintah.');
    }
}
