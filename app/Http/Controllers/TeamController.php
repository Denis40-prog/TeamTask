<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Http\Controllers\TeamUserController;
use App\Models\User;

class TeamController extends Controller
{
    public function show(int $teamId)
    {
        $team = Team::with('users')->find($teamId);

        $availableUsers = User::whereNotIn('id', $team->users->pluck('id'))->get();

        $teamLeader = $team->users->where('id', $team->leader_id)->first();

        return view('livewire.teamShow', compact('team', 'teamLeader', 'availableUsers'));
    }
}
