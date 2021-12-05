<?php

namespace App\Providers;

use App\Interfaces\CandidateRepositoryInterface;
use App\Interfaces\ElectionRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\CandidateRepository;
use App\Repositories\ElectionRepository;
use App\Repositories\UserRepository;
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
