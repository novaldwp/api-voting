<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(50)->create();
        // \App\Models\Election::factory(10)->create();
        // \App\Models\Candidate::factory(25)->create();

        foreach(\App\Models\Election::all() as $election)
        {
            $candidates = \App\Models\Candidate::inRandomOrder()->take(rand(2, 3))->pluck('id');
            foreach($candidates as $candidate)
            {
                $election->candidates()->attach($candidate);
            }
        }
    }
}
