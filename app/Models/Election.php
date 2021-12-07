<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'image'
    ];

    public function candidates()
    {
        return $this->belongsTomany(Candidate::class, 'candidate_election', 'election_id', 'candidate_id')->withTimestamps();
    }
}
