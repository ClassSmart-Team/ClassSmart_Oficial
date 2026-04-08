<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Observers\AssignmentObserver;
use App\Observers\SubmissionObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Assignment::observe(AssignmentObserver::class);
        Submission::observe(SubmissionObserver::class);
    }
}
