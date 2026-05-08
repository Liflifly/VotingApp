<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'theme',
        'status',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->slug)) {
                $event->slug = static::generateUniqueSlug($event->name);
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withPivot('role', 'metadata')
            ->withTimestamps();
    }

    public function elections()
    {
        return $this->hasMany(Election::class);
    }

    public function fieldDefinitions()
    {
        return $this->hasMany(EventFieldDefinition::class);
    }

    public function inviteTokens()
    {
        return $this->hasMany(EventInviteToken::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getUserRole(User $user): ?string
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;
        return $pivot?->role;
    }

    public function userHasRole(User $user, string|array $roles): bool
    {
        $role = $this->getUserRole($user);
        if ($role === null) return false;
        return in_array($role, (array) $roles);
    }

    public function voterFieldDefinitions()
    {
        return $this->fieldDefinitions()->where('target', 'voter')->orderBy('order');
    }

    public function candidateFieldDefinitions()
    {
        return $this->fieldDefinitions()->where('target', 'candidate')->orderBy('order');
    }

    public function activeElection(): ?Election
    {
        return $this->elections()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();
    }

    protected static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }
}
