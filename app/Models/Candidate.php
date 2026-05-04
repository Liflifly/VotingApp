<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'name',
        'order_number',
        'class',
        'photo',
        'vision',
        'mission',
        'program',
        'election_id',
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}
