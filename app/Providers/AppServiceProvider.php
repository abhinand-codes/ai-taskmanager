<?php

namespace App\Providers;

use App\Models\Task;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }

            return null;
        });

        Gate::policy(Task::class, TaskPolicy::class);

        Vite::prefetch(concurrency: 3);
    }
}