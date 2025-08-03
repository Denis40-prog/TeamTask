<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Team;

class DashboardTeams extends Component
{
    public $teams = [];

    protected $listeners = ['teamCreated' => 'refreshTeams'];

    public function mount()
    {
        $this->refreshTeams();
    }

    public function refreshTeams()
    {
        $this->teams = Auth::user()->teams()->latest()->get();
    }

    public function render()
    {
        return view('livewire.dashboard-teams');
    }
}

