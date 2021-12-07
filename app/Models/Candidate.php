<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'dob', 'address', 'vision', 'mission'
    ];

    public function elections()
    {
        return $this->belongsToMany(Election::class, 'candidate_election', 'candidate_id', 'election_id')
            ->withTimestamps();
    }
}
