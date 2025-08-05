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
    public $newProjectStartDate = '';
    public $newProjectEndDate = '';
    public $newProjectStatus = 'active';

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
        $this->reset(['newProjectName', 'newProjectDescription', 'newProjectStartDate', 'newProjectEndDate', 'newProjectStatus']);
    }

    public function createProject()
    {
        $this->validate([
            'newProjectName' => 'required|string|max:255',
            'newProjectDescription' => 'nullable|string|max:1000',
            'newProjectStartDate' => 'nullable|date',
            'newProjectEndDate' => 'nullable|date',
            'newProjectStatus' => 'required|in:active,archived',
        ]);

        Project::create([
            'name' => $this->newProjectName,
            'description' => $this->newProjectDescription,
            'start_date' => $this->newProjectStartDate ?: null,
            'end_date' => $this->newProjectEndDate ?: null,
            'status' => $this->newProjectStatus,
            'team_id' => $this->teamId,
            'owner_id' => Auth::id(),
        ]);

        $this->reset(['newProjectName', 'newProjectDescription', 'newProjectStartDate', 'newProjectEndDate', 'newProjectStatus', 'showCreateForm']);
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
