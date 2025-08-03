<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Team;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProjectComponent extends Component
{
    public $teamId;
    public $team;
    public $showCreateForm = false;
    public $newProjectName = '';
    public $newProjectDescription = '';

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->team = Team::findOrFail($teamId);

        // Vérifier que l'utilisateur fait partie de cette équipe
        if (!$this->team->users->contains(Auth::id())) {
            abort(403, 'Vous n\'êtes pas membre de cette équipe.');
        }
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->reset(['newProjectName', 'newProjectDescription']);
    }

    public function createProject()
    {
        $this->validate([
            'newProjectName' => 'required|string|max:255',
            'newProjectDescription' => 'nullable|string|max:1000',
        ]);

        Project::create([
            'name' => $this->newProjectName,
            'description' => $this->newProjectDescription,
            'team_id' => $this->teamId,
            'owner_id' => Auth::id(),
        ]);

        $this->reset(['newProjectName', 'newProjectDescription', 'showCreateForm']);
        session()->flash('message', 'Projet créé avec succès !');
    }

    public function render()
    {
        $projects = Project::where('team_id', $this->teamId)->get();

        return view('livewire.project-component', [
            'projects' => $projects
        ]);
    }
}
