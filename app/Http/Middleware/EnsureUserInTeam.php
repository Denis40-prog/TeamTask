<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserInTeam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Adapte à ton modèle: ex. $user->teams()->exists()
        if (!$user || !method_exists($user, 'teams') || !$user->teams()->exists()) {
            abort(403, "Accès réservé aux membres d'une équipe.");
        }

        return $next($request);
    }
}
