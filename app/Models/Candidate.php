<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'election_id',
        'order_number',
        'fields',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // ─── Dynamic Field Accessors ─────────────────────────────────────────────

    /**
     * Get a specific dynamic field value by its key.
     */
    public function getField(string $key): mixed
    {
        return $this->fields[$key] ?? null;
    }

    /**
     * Get the candidate's name from dynamic fields.
     */
    public function getNameAttribute(): string
    {
        return $this->getField('name') ?? 'Unnamed Candidate';
    }

    /**
     * Get the photo URL from dynamic fields, resolving storage path.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        $photo = $this->getField('photo');
        return $photo ? '/storage/' . $photo : null;
    }
}
