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
        'phone', // Ensure this is fillable
        'email', // Ensure this is fillable
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}