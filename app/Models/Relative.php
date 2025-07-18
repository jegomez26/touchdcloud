<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relative extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'name',
        'relationship_to_participant',
        'phone',
        'email',
    ];

    /**
     * Get the participant that this relative is associated with.
     */
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}