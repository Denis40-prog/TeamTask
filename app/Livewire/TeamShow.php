<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Team;
use App\Models\User;

class TeamShow extends Component
{
    public $teamId;
    public $team;
    public $teamLeader;
    public $newMemberId;
    public $availableUsers;

    public function mount($id)
    {
        $this->team = Team::with('users')->findOrFail($id);
        $this->teamLeader = $this->team->users()->where('is_leader', true)->first();
        $this->availableUsers = User::whereNotIn('id', $this->team->users->pluck('id'))->get();

    }

    public function addMember()
    {
        dd($this->newMemberId);

        if (!$this->newMemberId) {
            return;
        }

        $user = User::findOrFail($this->newMemberId);

        if ($user) {
            $this->team->users()->attach($user->id);
            $this->newMemberId = null;

            $this->mount($this->team->id);
            session()->flash('message', 'Membre ajouté avec succès.');
        }
    }

    public function removeMember($userId)
    {
        dd($userId);

        $user = User::findOrFail($userId);
        $this->team->users()->detach($user->id);

        $this->mount($this->team->id);
        session()->flash('message', 'Membre supprimé avec succès.');
    }

    public function render()
    {
        return view('livewire.teamShow');
    }
}
