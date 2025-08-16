<?php

namespace App\Livewire;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $showCreateForm = false;
    public $newTeamName = '';
    public $newTeamDescription = '';

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->reset(['newTeamName', 'newTeamDescription']);
    }

    public function createTeam()
    {
        $this->validate([
            'newTeamName' => 'required|string|max:255',
            'newTeamDescription' => 'nullable|string|max:1000',
        ]);

        $team = Team::create([
            'name' => $this->newTeamName,
            'description' => $this->newTeamDescription,
            'owner_id' => Auth::id(),
        ]);

        // Ajouter l'utilisateur à l'équipe
        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => Auth::id(),
            'role' => 'admin',
        ]);

        $this->reset(['newTeamName', 'newTeamDescription', 'showCreateForm']);

        $this->dispatch('flash', type: 'success', text: 'Équipe créée avec succès !');
    }

    public function render()
    {
        $user = Auth::user();

        // Si c'est l'admin du site, il voit toutes les équipes
        if ($user->role === User::ROLE_ADMIN) {
            $teams = Team::all();
        } else {
            // Sinon, seulement ses équipes
            $teams = $user->teams()->get();
        }

        return view('livewire.dashboard', [
            'teams' => $teams
        ]);
    }
}
