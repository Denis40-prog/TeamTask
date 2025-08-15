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
        Gate::define('viewWellness', fn (User $user) => in_array($user->role, ['admin', 'manager']));

        Gate::define('viewWellnessForTeam', function (User $user, Team $team) {
            if (!in_array($user->role, ['admin', 'manager'])) return false;
            return $user->teams()->whereKey($team->id)->exists();
        });
    }
}
