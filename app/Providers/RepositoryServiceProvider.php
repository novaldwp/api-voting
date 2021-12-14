<?php

namespace App\Providers;

use App\Interfaces\CandidateRepositoryInterface;
use App\Interfaces\ElectionRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\VotingRepositoryInterface;
use App\Repositories\CandidateRepository;
use App\Repositories\ElectionRepository;
use App\Repositories\UserRepository;
use App\Repositories\VotingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ElectionRepositoryInterface::class, ElectionRepository::class);
        $this->app->bind(CandidateRepositoryInterface::class, CandidateRepository::class);
        $this->app->bind(VotingRepositoryInterface::class, VotingRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
