<?php

use App\Http\Controllers\AdminCandidateController;
use App\Http\Controllers\AdminElectionController;
use App\Http\Controllers\AdminResultController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('welcome');
});

Route::get('/welcome', function () {
    $activeElection = \App\Models\Election::active()->first();
    $totalUsers = \App\Models\User::where('role', 'user')->count();
    return \Inertia\Inertia::render('Welcome', [
        'activeElection' => $activeElection,
        'totalUsers' => $totalUsers,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    $user = auth()->user();
    $activeElection = \App\Models\Election::active()->first();

    if (! $activeElection) {
        return \Inertia\Inertia::render('Dashboard', [
            'user' => $user,
            'activeElection' => null,
        ]);
    }

    $candidates = \App\Models\Candidate::where('election_id', $activeElection->id)->withCount('votes')->get();
    $totalVotes = \App\Models\Vote::where('election_id', $activeElection->id)->count();

    return \Inertia\Inertia::render('Dashboard', compact('user', 'candidates', 'totalVotes', 'activeElection'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/vote', [VoteController::class, 'index'])->name('vote.index');
    Route::post('/vote', [VoteController::class, 'store'])->name('vote.store');
    Route::get('/results', [VoteController::class, 'results'])->name('results.index');
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])->name('candidates.show');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.results.index');
    });
    Route::resource('elections', AdminElectionController::class);
    Route::post('elections/{election}/activate', [AdminElectionController::class, 'activate'])->name('elections.activate');
    Route::post('elections/{election}/end', [AdminElectionController::class, 'end'])->name('elections.end');
    Route::get('elections-history', [AdminElectionController::class, 'history'])->name('elections.history');

    Route::get('/elections/{election}/candidates', [AdminCandidateController::class, 'index'])->name('candidates.index');
    Route::get('/elections/{election}/candidates/create', [AdminCandidateController::class, 'create'])->name('candidates.create');
    Route::post('/elections/{election}/candidates', [AdminCandidateController::class, 'store'])->name('candidates.store');
    Route::get('/elections/{election}/candidates/{candidate}/edit', [AdminCandidateController::class, 'edit'])->name('candidates.edit');
    Route::put('/elections/{election}/candidates/{candidate}', [AdminCandidateController::class, 'update'])->name('candidates.update');
    Route::delete('/elections/{election}/candidates/{candidate}', [AdminCandidateController::class, 'destroy'])->name('candidates.destroy');
    Route::get('/results', [AdminResultController::class, 'index'])->name('results.index');
    Route::get('/election', [AdminElectionController::class, 'edit'])->name('election.edit');
    Route::put('/election', [AdminElectionController::class, 'update'])->name('election.update');
    Route::get('/admins', [AdminUserController::class, 'index'])->middleware('super_admin')->name('users.index');
    Route::put('/admins/{user}/role', [AdminUserController::class, 'updateRole'])->middleware('super_admin')->name('users.updateRole');
});

require __DIR__.'/auth.php';
