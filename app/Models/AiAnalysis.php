<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalysis extends Model
{
    protected $fillable = [
        'event_id',
        'election_id',
        'analysis_type',
        'prompt_hash',
        'response_text',
        'model_used',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeResultsSummary($query)
    {
        return $query->where('analysis_type', 'results_summary');
    }

    public function scopeRecommendation($query)
    {
        return $query->where('analysis_type', 'recommendation');
    }
}
