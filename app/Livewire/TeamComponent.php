<?php

namespace App\Livewire;

use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

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
