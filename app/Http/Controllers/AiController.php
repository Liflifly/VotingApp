<?php

namespace App\Http\Controllers;

use App\Models\AiAnalysis;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Event;
use App\Models\Vote;
use App\Services\AiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiController extends Controller
{
    public function __construct(protected AiService $ai) {}

    // ─── Voter: AI Recommendation Chat ───────────────────────────────────────

    /**
     * Show the AI recommendation chat page.
     * Only accessible to voters, and only when an election is active.
     */
    public function chat(Request $request, Event $event)
    {
        $userRole = $request->get('_event_role');

        // Only voters can use the recommendation chat
        if ($userRole !== 'voter') {
            return redirect()->route('events.dashboard', $event)
                ->with('error', 'AI recommendations are only available to voters.');
        }

        $activeElection = $event->activeElection();

        if (! $activeElection) {
            return redirect()->route('events.dashboard', $event)
                ->with('error', 'No active election to get recommendations for.');
        }

        $user = $request->user();

        // Don't show chat if voter already voted
        if ($user->hasVotedInElection($activeElection)) {
            return redirect()->route('events.dashboard', $event)
                ->with('info', 'You have already voted. AI recommendations are no longer available.');
        }

        // Build candidate context for the AI
        $candidates = Candidate::where('election_id', $activeElection->id)
            ->orderBy('order_number')
            ->get()
            ->map(fn($c) => [
                'id'       => $c->id,
                'fields'   => $c->fields,
                'photo_url'=> $c->photo_url,
            ]);

        $candidateFieldDefs = $event->candidateFieldDefinitions()->get()->map->toFormField();

        return Inertia::render('Event/AiChat', [
            'event'             => $event->only('id', 'name', 'slug', 'theme'),
            'election'          => $activeElection->only('id', 'name'),
            'candidates'        => $candidates,
            'candidateFields'   => $candidateFieldDefs,
        ]);
    }

    /**
     * Handle a voter recommendation request.
     * Takes voter's message + candidate context → returns AI recommendation.
     */
    public function recommend(Request $request, Event $event)
    {
        $userRole = $request->get('_event_role');

        if ($userRole !== 'voter') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message'          => ['required', 'string', 'max:1000'],
            'conversation'     => ['nullable', 'array', 'max:20'],
            'conversation.*.role'    => ['required', 'in:user,assistant'],
            'conversation.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $activeElection = $event->activeElection();
        if (! $activeElection) {
            return response()->json(['error' => 'No active election.'], 422);
        }

        // Build candidate context
        $candidates = Candidate::where('election_id', $activeElection->id)
            ->orderBy('order_number')
            ->get();

        $candidateContext = $candidates->map(function ($c) {
            $fields = is_string($c->fields) ? json_decode($c->fields, true) : ($c->fields ?? []);
            $lines = collect($fields)->map(fn($v, $k) => "{$k}: {$v}")->join(', ');
            return "Candidate #{$c->order_number}: {$lines}";
        })->join("\n");

        // System prompt
        $systemPrompt = <<<PROMPT
You are a voting assistant for the election "{$activeElection->name}" in the event "{$event->name}".
Your role is to help voters choose the best candidate based on their needs and preferences.
Be helpful, neutral, and factual. Do not make up information about candidates.
Only recommend from the candidates listed below.

CANDIDATES:
{$candidateContext}

Guidelines:
- Ask clarifying questions if the voter's needs are unclear.
- Explain your recommendation with specific reasons based on candidate information.
- If multiple candidates match, mention all of them.
- Keep your response concise and clear.
PROMPT;

        // Build conversation messages
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        // Add conversation history
        foreach ($request->conversation ?? [] as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }

        // Add current message
        $messages[] = ['role' => 'user', 'content' => $request->message];

        try {
            $response = $this->ai->chat($messages);
            return response()->json([
                'response' => $response,
                'model'    => $this->ai->getModel(),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }

    // ─── Admin: Results Analysis ──────────────────────────────────────────────

    /**
     * Generate an AI analysis of election results.
     * Cached in ai_analyses table to avoid redundant Ollama calls.
     */
    public function analyzeResults(Request $request, Event $event)
    {
        $request->validate([
            'election_id' => ['required', 'integer', 'exists:elections,id'],
        ]);

        $election = Election::where('id', $request->election_id)
            ->where('event_id', $event->id)
            ->firstOrFail();

        // Build data for analysis
        $candidates = Candidate::where('election_id', $election->id)
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->get();

        $totalVotes = $candidates->sum('votes_count');
        $totalVoters = $event->users()->wherePivot('role', 'voter')->count();
        $participationRate = $totalVoters > 0 ? round(($totalVotes / $totalVoters) * 100, 1) : 0;

        // Build a prompt hash for caching
        $promptData = [
            'election_id'   => $election->id,
            'total_votes'   => $totalVotes,
            'vote_breakdown'=> $candidates->pluck('votes_count', 'id')->toArray(),
        ];
        $promptHash = hash('sha256', json_encode($promptData));

        // Check cache
        $cached = AiAnalysis::where('election_id', $election->id)
            ->where('analysis_type', 'results_summary')
            ->where('prompt_hash', $promptHash)
            ->first();

        if ($cached) {
            return response()->json([
                'analysis' => $cached->response_text,
                'cached'   => true,
                'model'    => $cached->model_used,
            ]);
        }

        // Build candidate breakdown text
        $breakdown = $candidates->map(function ($c) use ($totalVotes) {
            $fields = is_string($c->fields) ? json_decode($c->fields, true) : ($c->fields ?? []);
            $name = collect($fields)->first() ?? "Candidate #{$c->order_number}";
            $pct  = $totalVotes > 0 ? round(($c->votes_count / $totalVotes) * 100, 1) : 0;
            return "{$name}: {$c->votes_count} votes ({$pct}%)";
        })->join("\n");

        $prompt = <<<PROMPT
Analyze the following voting results for the election "{$election->name}":

RESULTS:
{$breakdown}

Total votes cast: {$totalVotes}
Total eligible voters: {$totalVoters}
Participation rate: {$participationRate}%

Please provide:
1. A brief summary of the results
2. Key observations (e.g., margin of victory, voter turnout)
3. Any notable patterns
Keep the analysis concise and professional (2-3 paragraphs).
PROMPT;

        try {
            $analysis = $this->ai->generate($prompt);

            // Cache the result
            AiAnalysis::create([
                'event_id'     => $event->id,
                'election_id'  => $election->id,
                'analysis_type'=> 'results_summary',
                'prompt_hash'  => $promptHash,
                'response_text'=> $analysis,
                'model_used'   => $this->ai->getModel(),
            ]);

            return response()->json([
                'analysis' => $analysis,
                'cached'   => false,
                'model'    => $this->ai->getModel(),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }
}
