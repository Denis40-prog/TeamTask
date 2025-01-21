<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\TeamUser;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        // Charger les relations nécessaires
        $project->load('team', 'team.users', 'tasks');

        // Récupérer les utilisateurs de l'équipe liée au projet
        $teamUsers = $project->team->users;

        $comments = $project->comments()->with('user')->get();

        return view('project', compact('project', 'teamUsers', 'comments'));
    }

}
