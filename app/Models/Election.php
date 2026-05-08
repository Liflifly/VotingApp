<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'status',
        'total_voters',
        'notes',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeForEvent($query, Event $event)
    {
        return $query->where('event_id', $event->id);
    }

    // ─── State Checks ─────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->betweenIncluded($this->starts_at, $this->ends_at);
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    /**
     * Calculate voter participation rate for this election.
     * Uses the event_user pivot as the source of truth for total eligible voters.
     */
    public function participationRate(): float
    {
        $totalVoters = $this->total_voters > 0
            ? $this->total_voters
            : $this->event->users()->wherePivot('role', 'voter')->count();

        if ($totalVoters === 0) {
            return 0.0;
        }

        return round(($this->votes()->count() / $totalVoters) * 100, 2);
    }
}
