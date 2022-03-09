<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'candidate_id', 'election_id'
    ];

    public function candidates()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function elections()
    {
        return $this->belongsTo(Election::class, 'election_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

