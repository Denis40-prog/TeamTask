<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
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
    public $newMemberEmail = '';
    public bool $isAdmin;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->team = Team::with(['users' => function ($query) {
            $query->withPivot('role');
        }])->findOrFail($teamId);

        if (!$this->team->users->contains(Auth::id())) {
            abort(403, 'Vous n\'êtes pas membre de cette équipe.');
        }

        $this->checkIfAdmin();
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

    public function updatedTeam()
    {
        $this->checkIfAdmin();
    }

    private function checkIfAdmin()
    {
        $currentUser = auth()->user();
        $this->isAdmin = $this->team->users
            ->where('id', $currentUser->id)
            ->first()?->pivot->role === 'admin';
    }

    public function addMember()
    {
        $this->validate([
            'newMemberEmail' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $this->newMemberEmail)->first();

        if ($this->team->users->contains($user)) {
            session()->flash('message', 'Ce membre est déjà dans l\'équipe.');
            return;
        }

        $this->team->users()->attach($user);
        $this->newMemberEmail = '';
        $this->team->refresh();
        session()->flash('message', 'Membre ajouté avec succès.');
    }

    public function removeMember($userId)
    {
        $this->team->users()->detach($userId);
        $this->team->refresh();
        session()->flash('message', 'Membre retiré de l\'équipe.');
    }

    public function render()
    {
        $projects = Project::where('team_id', $this->teamId)->get();

        return view('livewire.project-component', [
            'projects' => $projects,
        ]);
    }
}
