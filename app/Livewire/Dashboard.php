<?php

namespace App\Livewire;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Dashboard extends Component
{
    use WithPagination;

    // Création d'équipe
    public bool $showCreateForm = false;
    public string $newTeamName = '';
    public string $newTeamDescription = '';

    // Liste / filtre / tri
    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;
        $this->reset(['newTeamName', 'newTeamDescription']);
    }

    public function createTeam(): void
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

        // Ajouter l'utilisateur créateur comme admin de l'équipe
        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => Auth::id(),
            'role'    => 'admin', // rôle pivot: 'admin' | 'user' | 'rh'
        ]);

        $this->reset(['newTeamName', 'newTeamDescription', 'showCreateForm']);

        $this->dispatch('flash', type: 'success', text: 'Équipe créée avec succès !');
    }

    public function deleteTeam(Team $team): void
    {
        if (! Gate::allows('deleteTeam', $team)) {
            $this->dispatch('flash', type: 'error', text: 'Vous n\'avez pas les permissions pour supprimer cette équipe.');
            return;
        }

        try {
            $teamName = $team->name;
            $team->delete();
            $this->dispatch('flash', type: 'success', text: 'L\'équipe "'.$teamName.'" a été supprimée avec succès.');
        } catch (\Throwable $e) {
            $this->dispatch('flash', type: 'error', text: 'Une erreur est survenue lors de la suppression de l\'équipe.');
        }
    }

    /* --------- Comportements liste --------- */

    // Reset pagination quand la recherche change
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Toggle tri / reset page quand on change de champ
    public function sortBy(string $field): void
    {
        $allowed = ['name', 'created_at'];
        if (! in_array($field, $allowed, true)) {
            $field = 'name';
        }

        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
            $this->resetPage();
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Base query
        $query = Team::query()->select('teams.*');

        // Scope d'appartenance: si pas admin site, ne montrer que les équipes dont il est membre
        if ($user->role !== User::ROLE_ADMIN) {
            $query->whereHas('users', fn ($q) => $q->where('users.id', $user->id));
        }

        // Recherche (reste dans le scope ci-dessus)
        if (filled($this->search)) {
            $s = '%' . str_replace('%', '\%', $this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('teams.name', 'like', $s)
                  ->orWhere('teams.description', 'like', $s);
            });
        }

        // Compteurs attendus par l'UI
        $query->withCount(['users', 'projects']);

        // Tri sécurisé (qualifier)
        $allowed = ['name', 'created_at'];
        if (! in_array($this->sortBy, $allowed, true)) {
            $this->sortBy = 'name';
        }
        $query->orderBy('teams.' . $this->sortBy, $this->sortDirection);

        // Pagination
        $teams = $query->paginate(10);

        return view('livewire.dashboard', [
            'teams' => $teams,
        ]);
    }
}
