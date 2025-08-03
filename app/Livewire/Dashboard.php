<?php

namespace App\Livewire;

use App\Models\Team;
use App\Models\TeamUser;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $showCreateForm = false;
    public $newTeamName = '';

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->reset(['newTeamName']);
    }

    public function createTeam()
    {
        $this->validate([
            'newTeamName' => 'required|string|max:255',
        ]);

        $team = Team::create([
            'name' => $this->newTeamName,
            'owner_id' => Auth::id(),
        ]);

        // Ajouter l'utilisateur à l'équipe
        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => Auth::id(),
            'role' => 'owner',
        ]);

        $this->reset(['newTeamName', 'showCreateForm']);
        session()->flash('message', 'Équipe créée avec succès !');
    }

    public function render()
    {
        $teams = Auth::user()->teams()->get();

        return view('livewire.dashboard', [
            'teams' => $teams
        ]);
    }
}
