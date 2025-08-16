<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UserManagement extends Component
{
    use WithPagination;

    // Filtres
    public $search = '';
    public $emailFilter = '';
    public $teamFilter = '';
    public $roleFilter = '';

    // Tri
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    // Modal de confirmation
    public $showPromoteModal = false;
    public $userToPromote = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'emailFilter' => ['except' => ''],
        'teamFilter' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        // Vérifier que l'utilisateur est admin du site
        if (Auth::user()->role !== User::ROLE_ADMIN) {
            abort(403, 'Accès refusé. Réservé aux administrateurs du site.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEmailFilter()
    {
        $this->resetPage();
    }

    public function updatingTeamFilter()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
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

    public function clearFilters()
    {
        $this->reset(['search', 'emailFilter', 'teamFilter', 'roleFilter']);
        $this->resetPage();
    }

    public function confirmPromoteUser($userId)
    {
        $this->userToPromote = User::find($userId);
        $this->showPromoteModal = true;
    }

    public function promoteToAdmin()
    {
        if ($this->userToPromote && $this->userToPromote->role !== User::ROLE_ADMIN) {
            $this->userToPromote->update(['role' => User::ROLE_ADMIN]);

            $this->dispatch('flash', [
                'type' => 'success',
                'text' => "L'utilisateur {$this->userToPromote->name} a été promu administrateur du site."
            ]);
        }

        $this->reset(['showPromoteModal', 'userToPromote']);
    }

    public function demoteFromAdmin($userId)
    {
        $user = User::find($userId);
        if ($user && $user->role === User::ROLE_ADMIN && $user->id !== Auth::id()) {
            $user->update(['role' => User::ROLE_USER]);

            $this->dispatch('flash', [
                'type' => 'success',
                'text' => "L'utilisateur {$user->name} n'est plus administrateur du site."
            ]);
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->emailFilter, function ($query) {
                $query->where('email', 'like', "%{$this->emailFilter}%");
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->when($this->teamFilter, function ($query) {
                $query->whereHas('teams', function ($q) {
                    $q->where('teams.id', $this->teamFilter);
                });
            })
            ->with(['teams' => function ($query) {
                $query->select('teams.id', 'teams.name');
            }])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        $teams = Team::select('id', 'name')->orderBy('name')->get();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'teams' => $teams,
        ]);
    }
}
