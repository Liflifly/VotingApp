<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * SECURITY: Only basic profile fields are mass-assignable.
     * Roles are managed exclusively through the event_user pivot.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'avatar_original',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withPivot('role', 'metadata')
            ->withTimestamps();
    }

    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────

    /**
     * Get this user's role within a specific event.
     */
    public function roleInEvent(Event $event): ?string
    {
        return $this->events()
            ->where('event_id', $event->id)
            ->first()?->pivot->role;
    }

    /**
     * Check if the user has a given role (or any of the given roles) in an event.
     */
    public function hasRoleInEvent(Event $event, string|array $roles): bool
    {
        $role = $this->roleInEvent($event);
        if ($role === null) return false;
        return in_array($role, (array) $roles);
    }

    /**
     * Check if the user has voted in a specific election.
     * Queries votes table directly (source of truth).
     */
    public function hasVotedInElection(Election $election): bool
    {
        return $this->votes()
            ->where('election_id', $election->id)
            ->exists();
    }

    /**
     * Get this user's dynamic metadata for a specific event.
     */
    public function metadataInEvent(Event $event): ?array
    {
        $pivot = $this->events()
            ->where('event_id', $event->id)
            ->first()?->pivot;

        if ($pivot && is_string($pivot->metadata)) {
            return json_decode($pivot->metadata, true);
        }

        return $pivot?->metadata;
    }
}
