<?php

namespace App\Livewire;

use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TeamComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $showCreateForm = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function showCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function deleteTeam(Team $team)
    {
        // Vérifier les permissions
        if (!Gate::allows('deleteTeam', $team)) {
            session()->flash('error', 'Vous n\'avez pas les permissions pour supprimer cette équipe.');
            return;
        }

        try {
            // Supprimer l'équipe et toutes ses relations
            $team->delete();
            session()->flash('success', 'L\'équipe "' . $team->name . '" a été supprimée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la suppression de l\'équipe.');
        }
    }

    public function render()
    {
        $user = Auth::user();

        $teams = Team::query()
            ->when($user->role !== 'admin', function ($query) {
                // Si pas admin du site, filtrer par appartenance à l'équipe
                $query->whereHas('users', function ($q) {
                    $q->where('users.id', Auth::id());
                });
            })
            ->when($this->search, function ($query) {
                $search = "%{$this->search}%";
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search);
                });
            })
            ->withCount(['users', 'projects'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.team-component', [
            'teams' => $teams
        ]);
    }
}
