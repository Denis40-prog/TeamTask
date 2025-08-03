<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Team;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class CreateTeamForm extends Component
{
    public $name = '';
    public $showForm = false;

    public function create()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $team = Team::create([
            'name' => $this->name,
            'owner_id' => Auth::id(),
        ]);

        // table link
        $team->users()->attach(auth()->id(), ['role' => 'owner']);

        Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Vous avez créé l’équipe « ' . $team->name . ' »',
        ]);

        session()->flash('success', 'Équipe créée avec succès.');

        $this->reset(['name', 'showForm']);

        $this->dispatch('teamCreated');
        $this->dispatch('notificationCreated');
    }

    public function render()
    {
        return view('livewire.create-team-form');
    }
}
