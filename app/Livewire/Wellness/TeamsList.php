<?php

namespace App\Livewire\Wellness;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeamsList extends Component
{
    public array $teams = [];

    public function mount(): void
    {
        $this->teams = Auth::user()
            ->teams()
            ->select('teams.id', 'teams.name', 'teams.created_at')
            ->orderBy('teams.name')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.wellness.teams-list')->title('Suivi météo — Mes équipes');
    }
}
