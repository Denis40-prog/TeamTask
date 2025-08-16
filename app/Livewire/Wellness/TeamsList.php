<?php

namespace App\Livewire\Wellness;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;
use App\Models\Team;
use App\Models\TeamUser;

class TeamsList extends Component
{
    public array $teams = [];

    public function mount(): void
    {
        $user = Auth::user();

        // Si l'utilisateur est admin du site, il peut voir toutes les équipes
        if ($user->role === User::ROLE_ADMIN) {
            $this->teams = Team::select('id', 'name', 'created_at')
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            // Sinon, seulement les équipes où il a accès au wellness (admin ou RH)
            $teamIds = TeamUser::where('user_id', $user->id)
                ->whereIn('role', [TeamUser::TEAM_ROLE_ADMIN, TeamUser::TEAM_ROLE_RH])
                ->pluck('team_id');

            $this->teams = Team::whereIn('id', $teamIds)
                ->select('id', 'name', 'created_at')
                ->orderBy('name')
                ->get()
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.wellness.teams-list');
    }
}
