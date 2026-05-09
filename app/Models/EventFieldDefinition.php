<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFieldDefinition extends Model
{
    protected $fillable = [
        'event_id',
        'target',
        'key',
        'label',
        'type',
        'options',
        'required',
        'is_primary',
        'order',
    ];

    protected $casts = [
        'options'    => 'array',
        'required'   => 'boolean',
        'is_primary' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Build Inertia-safe array for frontend rendering.
     */
    public function toFormField(): array
    {
        return [
            'id'       => $this->id,
            'key'      => $this->key,
            'label'    => $this->label,
            'type'     => $this->type,
            'options'  => $this->options ?? [],
            'required'   => $this->required,
            'is_primary' => $this->is_primary,
            'order'      => $this->order,
        ];
    }
}
