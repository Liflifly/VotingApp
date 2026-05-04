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

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        $user->update(['role' => $data['role']]);

        return redirect()->route('admin.users.index')->with('success', 'Boom! Pangkat admin berhasil diubah sesuai perintah.');
    }
}

