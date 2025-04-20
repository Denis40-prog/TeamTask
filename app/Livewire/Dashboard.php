<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\Project;

class Dashboard extends Component
{
    public $teams;
    public $projects;

    public function mount()
    {
        $user = Auth::user();

        // Récupérer les équipes et projets associés à l'utilisateur
        $this->teams = $user->teams; // En supposant que le modèle User a une relation "teams"
        $this->projects = Project::whereIn('team_id', $this->teams->pluck('id'))->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
