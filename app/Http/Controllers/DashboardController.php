<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        return view('dashboard', [
            'teams' => $user->teams,
            'notifications' => $user->notifications()->latest()->take(10)->get(),
        ]);
    }
}
