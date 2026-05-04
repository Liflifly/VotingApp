<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'total_voters',
        'notes',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * BUG FIX: Sebelumnya ada spasi ekstra di ' <=' → menjadi '<='
     * Ini menyebabkan scope active tidak pernah return data karena
     * query jadi: WHERE starts_at ' <=' now() (syntax error / salah).
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

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
     * BUG FIX: Sebelumnya pakai $this->total_voters yang hanya diset saat election
     * di-end. Saat election masih active, nilainya 0/null sehingga selalu return 0%.
     * Sekarang hitung langsung dari relasi votes & User::count() sebagai fallback.
     */
    public function participationRate(): float
    {
        $totalVoters = $this->total_voters > 0
            ? $this->total_voters
            : \App\Models\User::where('role', 'user')->count();

        if ($totalVoters === 0) {
            return 0.0;
        }

        $totalVotes = $this->votes()->count();

        return round(($totalVotes / $totalVoters) * 100, 2);
    }
}
