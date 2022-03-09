<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'image', 'status'
    ];

    public function candidates()
    {
        // return $this->belongsToMany(Candidate::class, 'candidate_election', 'election_id', 'candidate_id')->withTimestamps();
        return $this->belongsToMany(Candidate::class);
    }

    public function votings()
    {
        return $this->hasMany(Voting::class);
    }
}
