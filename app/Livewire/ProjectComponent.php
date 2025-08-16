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
    public bool $isTeamAdmin;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->team = Team::with(['users' => function ($query) {
            $query->withPivot('role');
        }])->findOrFail($teamId);

        if (! $this->team->users->pluck('id')->contains(Auth::id())) {
            abort(403, 'Vous n\'êtes pas membre de cette équipe.');
        }

        $this->checkIfAdmin();
        $this->checkIfTeamAdmin();
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

        $this->dispatch('flash', type: 'success', text: 'Projet créée avec succès !');
    }

    public function updatedTeam()
    {
        $this->checkIfAdmin();
        $this->checkIfTeamAdmin();
    }

    private function checkIfAdmin()
    {
        $this->isAdmin = Auth::user()->role === 'admin';
    }

    private function checkIfTeamAdmin()
    {
        $currentUser = Auth::user();

        if ($this->team->owner_id == $currentUser->id) {
            $this->isTeamAdmin = true;
            return;
        }

        $this->isTeamAdmin = $this->team->users
            ->where('id', $currentUser->id)
            ->first()?->pivot->role === 'admin';
    }

    public function addMember()
    {
        abort_unless($this->isTeamAdmin, 403);

        $this->validate([
            'newMemberEmail' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $this->newMemberEmail)->first();

        if ($this->team->users()->whereKey($user->id)->exists()) {
            $this->dispatch('flash', type: 'info', text: 'Ce membre est déjà dans l\'équipe !');

            $this->newMemberEmail = '';
            return;
        }

        $this->team->users()->attach($user, ['role' => 'member']);
        $this->newMemberEmail = '';
        $this->team->refresh();

        $this->dispatch('flash', type: 'success', text: 'Membre ajouté avec succès !');
    }

    public function removeMember($userId)
    {
        abort_unless($this->isTeamAdmin, 403);

        $this->team->users()->detach($userId);
        $this->team->refresh();

        $this->dispatch('flash', type: 'success', text: 'Membre retiré de l\'équipe !');
    }

    public function promoteToAdmin($userId)
    {
        abort_unless($this->isTeamAdmin, 403);

        if (!$this->team->users()->whereKey($userId)->exists()) {
            $this->dispatch('flash', type: 'error', text: 'Utilisateur non trouvé dans cette équipe !');
            return;
        }

        $this->team->users()->updateExistingPivot($userId, ['role' => 'admin']);
        $this->team->refresh();

        $user = User::find($userId);
        $this->dispatch('flash', type: 'success', text: $user->name . ' a été promu administrateur de l\'équipe !');
    }

    public function demoteFromAdmin($userId)
    {
        abort_unless($this->isTeamAdmin, 403);

        if (!$this->team->users()->whereKey($userId)->exists()) {
            $this->dispatch('flash', type: 'error', text: 'Utilisateur non trouvé dans cette équipe !');
            return;
        }

        if ($this->team->owner_id == $userId) {
            $this->dispatch('flash', type: 'error', text: 'Impossible de rétrograder le propriétaire de l\'équipe !');
            return;
        }

        $this->team->users()->updateExistingPivot($userId, ['role' => 'member']);
        $this->team->refresh();

        $user = User::find($userId);
        $this->dispatch('flash', type: 'success', text: $user->name . ' n\'est plus administrateur de l\'équipe !');
    }

    public function render()
    {
        $projects = Project::where('team_id', $this->teamId)->get();

        return view('livewire.project-component', [
            'projects' => $projects,
        ]);
    }
}
