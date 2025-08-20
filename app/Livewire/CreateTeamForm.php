<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Team;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class CreateTeamForm extends Component
{
    public $name = '';
    public $description = '';
    public $showForm = false;

    public function create()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team = Team::create([
            'name' => $this->name,
            'description' => $this->description,
            'owner_id' => Auth::id(),
        ]);

        // table link - Le créateur devient automatiquement admin de l'équipe
        $team->users()->attach(Auth::id(), ['role' => 'admin']);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Vous avez créé l’équipe « ' . $team->name . ' »',
        ]);

        $this->reset(['name', 'description', 'showForm']);

        $this->dispatch('teamCreated');
        $this->dispatch('notificationCreated');

        $this->dispatch('flash', type: 'success', text: 'Équipe créée avec succès !');
    }

    public function render()
    {
        return view('livewire.create-team-form');
    }
}
