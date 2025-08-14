<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Gate::define('viewWellness', fn (User $user) => $user->isAdmin());
        Gate::define('viewWellness', fn (User $user) => $user->role === 'admin');

        Gate::define('viewWellnessForTeam', function (User $user, Team $team) {
            if ($user->role !== 'admin') return false;
            return $user->teams()->whereKey($team->id)->exists();
        });
    }
}
