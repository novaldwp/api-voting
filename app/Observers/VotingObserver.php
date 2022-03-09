<?php

namespace App\Observers;

use App\Interfaces\ElectionRepositoryInterface;
use App\Models\Voting;
use Illuminate\Support\Facades\Redis;

class VotingObserver
{
    private $electionRepository;

    public function __construct(ElectionRepositoryInterface $electionRepository)
    {
        $this->electionRepository = $electionRepository;
    }

    /**
     * Handle the Voting "created" event.
     *
     * @param  \App\Models\Voting  $voting
     * @return void
     */
    public function created(Voting $voting)
    {

    }

    /**
     * Handle the Voting "updated" event.
     *
     * @param  \App\Models\Voting  $voting
     * @return void
     */
    public function updated(Voting $voting)
    {
        //
    }

    /**
     * Handle the Voting "deleted" event.
     *
     * @param  \App\Models\Voting  $voting
     * @return void
     */
    public function deleted(Voting $voting)
    {
        //
    }

    /**
     * Handle the Voting "restored" event.
     *
     * @param  \App\Models\Voting  $voting
     * @return void
     */
    public function restored(Voting $voting)
    {
        //
    }

    /**
     * Handle the Voting "force deleted" event.
     *
     * @param  \App\Models\Voting  $voting
     * @return void
     */
    public function forceDeleted(Voting $voting)
    {
        //
    }
}
