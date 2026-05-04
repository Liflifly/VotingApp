<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * SECURITY: role, has_voted, voted_election_id are intentionally excluded
     * to prevent mass assignment privilege escalation attacks.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nis',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * BUG-02 FIX: Query langsung ke tabel votes sebagai source of truth,
     * bukan membandingkan kolom voted_election_id yang bisa out-of-sync.
     */
    public function hasVotedInElection(Election $election): bool
    {
        return $this->votes()
            ->where('election_id', $election->id)
            ->exists();
    }

    /**
     * BUG-02 FIX: Diganti ke hasMany karena satu user bisa vote
     * di banyak election (berbeda periode).
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
