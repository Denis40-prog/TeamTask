<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Team;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Récupérer les équipes de l'utilisateur
        $teams = $user->teams; // Relation "teams" dans le modèle User

        // Récupérer les projets associés à ces équipes
        $projects = Project::whereIn('team_id', $teams->pluck('id'))->get();

        return view('dashboard', compact('projects', 'teams'));
    }
}

