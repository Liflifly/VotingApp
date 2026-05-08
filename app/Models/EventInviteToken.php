<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventInviteToken extends Model
{
    protected $fillable = [
        'event_id',
        'token',
        'role',
        'expires_at',
        'used_at',
        'used_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeValid($query)
    {
        return $query
            ->whereNull('used_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isValid(): bool
    {
        return $this->used_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function markUsed(User $user): void
    {
        $this->update([
            'used_at' => now(),
            'used_by' => $user->id,
        ]);
    }

    /**
     * Generate a new secure token string (plain text — store hashed separately if needed).
     */
    public static function generateToken(): string
    {
        return Str::upper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
    }
}
