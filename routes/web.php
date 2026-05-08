<?php

use App\Http\Controllers\AdminCandidateController;
use App\Http\Controllers\AdminElectionController;
use App\Http\Controllers\AdminResultController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Welcome / Landing ────────────────────────────────────────────────────────

Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

// ─── User Dashboard (post-login home) ────────────────────────────────────────

Route::middleware('auth')->get('/dashboard', function (\Illuminate\Http\Request $request) {
    $user = $request->user();

    // Events the user created (super_admin)
    $createdEvents = $user->createdEvents()
        ->withCount(['users as voter_count' => fn($q) => $q->where('event_user.role', 'voter')])
        ->latest()
        ->get()
        ->map(fn($e) => [
            'id'                  => $e->id,
            'name'                => $e->name,
            'slug'                => $e->slug,
            'status'              => $e->status,
            'voter_count'         => $e->voter_count,
            'results_visibility'  => $e->results_visibility,
            'voter_access_token'  => $e->voter_access_token,
            'admin_access_token'  => $e->admin_access_token,
            'active_election'     => $e->activeElection()?->only('id', 'name', 'status'),
        ]);

    // Events the user joined (voter or admin role)
    $joinedEvents = $user->events()
        ->wherePivotIn('role', ['voter', 'admin'])
        ->latest('event_user.created_at')
        ->get()
        ->map(fn($e) => [
            'id'             => $e->id,
            'name'           => $e->name,
            'slug'           => $e->slug,
            'status'         => $e->status,
            'role'           => $e->pivot->role,
            'active_election'=> $e->activeElection()?->only('id', 'name', 'status'),
        ]);

    return Inertia::render('UserDashboard', compact('createdEvents', 'joinedEvents'));
})->name('dashboard');

// ─── Event Creation (requires authentication) ─────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
});

// ─── Token-Based Join Links (Phase 4) ────────────────────────────────────────

Route::middleware('auth')->group(function () {
    // Voter join via link/QR
    Route::get('/join/v/{token}', [EventController::class, 'joinViaVoterToken'])->name('events.join.voter.token');
    Route::post('/join/v/{token}', [EventController::class, 'registerAsVoter'])->name('events.join.voter.submit')
        ->middleware('throttle:10,1');

    // Admin join via link/QR (then enters invite token)
    Route::get('/join/a/{token}', [EventController::class, 'joinViaAdminToken'])->name('events.join.admin.token');
    Route::post('/join/a/{token}', [EventController::class, 'registerAsAdmin'])->name('events.join.admin.submit')
        ->middleware('throttle:10,1');
});

// ─── Event Public Landing (slug-based) ────────────────────────────────────────

Route::get('/e/{event:slug}', [EventController::class, 'show'])->name('events.show');

// ─── Legacy join routes (kept for backward compat) ────────────────────────────

Route::get('/e/{event:slug}/join', [EventController::class, 'join'])->name('events.join');
Route::post('/e/{event:slug}/join/admin', [EventController::class, 'joinAsAdmin'])->name('events.join.admin')
    ->middleware('throttle:10,1');
Route::post('/e/{event:slug}/join/voter', [EventController::class, 'joinAsVoter'])->name('events.join.voter')
    ->middleware('throttle:10,1');

// ─── Event-Scoped Routes (all require auth + event membership) ─────────────────

Route::prefix('/e/{event:slug}')
    ->middleware(['auth', 'event.context'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', function (\Illuminate\Http\Request $request, \App\Models\Event $event) {
            $user           = $request->user();
            $activeElection = $event->activeElection();
            $totalVoters    = $event->users()->wherePivot('role', 'voter')->count();
            $userRole       = $request->get('_event_role');

            $candidates = [];
            if ($activeElection) {
                $candidates = \App\Models\Candidate::where('election_id', $activeElection->id)
                    ->orderBy('order_number')
                    ->get()
                    ->map(fn ($c) => [
                        'id'           => $c->id,
                        'order_number' => $c->order_number,
                        'fields'       => $c->fields,
                        'photo_url'    => $c->photo_url,
                    ]);
            }

            return Inertia::render('Dashboard', compact(
                'event',
                'activeElection',
                'totalVoters',
                'userRole',
                'candidates',
            ));
        })->name('events.dashboard');

        // ─── Voter Routes ────────────────────────────────────────────────────

        Route::get('/vote', [VoteController::class, 'index'])->name('events.vote.index');
        Route::post('/vote', [VoteController::class, 'store'])->name('events.vote.store')
            ->middleware('throttle:5,1');
        Route::get('/results', [VoteController::class, 'results'])->name('events.results');
        Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])->name('events.candidates.show');

        // ─── AI Routes (voter + admin) ────────────────────────────────────────

        Route::get('/ai', [AiController::class, 'chat'])->name('events.ai.chat');
        Route::post('/ai/recommend', [AiController::class, 'recommend'])->name('events.ai.recommend')
            ->middleware('throttle:20,1');

        // ─── Admin Routes (admin + super_admin) ──────────────────────────────

        Route::middleware('admin')->prefix('admin')->group(function () {
            // Elections
            Route::get('/elections', [AdminElectionController::class, 'index'])->name('events.admin.elections.index');
            Route::get('/elections/create', [AdminElectionController::class, 'create'])->name('events.admin.elections.create');
            Route::get('/elections/history', [AdminElectionController::class, 'history'])->name('events.admin.elections.history');
            Route::post('/elections', [AdminElectionController::class, 'store'])->name('events.admin.elections.store');
            Route::get('/elections/{election}', [AdminElectionController::class, 'show'])->name('events.admin.elections.show');
            Route::get('/elections/{election}/edit', [AdminElectionController::class, 'edit'])->name('events.admin.elections.edit');
            Route::put('/elections/{election}', [AdminElectionController::class, 'update'])->name('events.admin.elections.update');
            Route::delete('/elections/{election}', [AdminElectionController::class, 'destroy'])->name('events.admin.elections.destroy');
            Route::post('/elections/{election}/activate', [AdminElectionController::class, 'activate'])->name('events.admin.elections.activate');
            Route::post('/elections/{election}/end', [AdminElectionController::class, 'end'])->name('events.admin.elections.end');

            // Candidates (nested under elections)
            Route::get('/elections/{election}/candidates', [AdminCandidateController::class, 'index'])->name('events.admin.candidates.index');
            Route::get('/elections/{election}/candidates/create', [AdminCandidateController::class, 'create'])->name('events.admin.candidates.create');
            Route::post('/elections/{election}/candidates', [AdminCandidateController::class, 'store'])->name('events.admin.candidates.store');
            Route::get('/elections/{election}/candidates/{candidate}/edit', [AdminCandidateController::class, 'edit'])->name('events.admin.candidates.edit');
            Route::put('/elections/{election}/candidates/{candidate}', [AdminCandidateController::class, 'update'])->name('events.admin.candidates.update');
            Route::delete('/elections/{election}/candidates/{candidate}', [AdminCandidateController::class, 'destroy'])->name('events.admin.candidates.destroy');

            // Results (with AI analysis)
            Route::get('/results', [AdminResultController::class, 'index'])->name('events.admin.results');
            Route::post('/results/ai-analysis', [AiController::class, 'analyzeResults'])->name('events.admin.results.ai')
                ->middleware('throttle:5,1');

            // Users / Members
            Route::get('/users', [AdminUserController::class, 'index'])->name('events.admin.users.index');
        });

        // ─── Super Admin Routes ───────────────────────────────────────────────

        Route::middleware('super_admin')->prefix('admin')->group(function () {
            Route::get('/settings', [EventSettingsController::class, 'edit'])->name('events.admin.settings');
            Route::put('/settings', [EventSettingsController::class, 'update'])->name('events.admin.settings.update');
            Route::post('/settings/tokens', [EventSettingsController::class, 'generateToken'])->name('events.admin.tokens.generate');
            Route::delete('/settings/tokens/{token}', [EventSettingsController::class, 'revokeToken'])->name('events.admin.tokens.revoke');
            Route::post('/settings/regenerate-links', [EventSettingsController::class, 'regenerateLinks'])->name('events.admin.links.regenerate');
            Route::put('/settings/fields', [EventSettingsController::class, 'updateFields'])->name('events.admin.fields.update');
            Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('events.admin.users.role');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('events.admin.users.destroy');
        });
    });

// ─── Profile ──────────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Auth Routes ──────────────────────────────────────────────────────────────

require __DIR__ . '/auth.php';
