<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST DES PERMISSIONS DE SUPPRESSION ===\n\n";

try {
    $admin = \App\Models\User::where('email', 'root@teamtask.com')->first();
    $user = \App\Models\User::where('email', '!=', 'root@teamtask.com')->first();
    $team = \App\Models\Team::first();
    $project = \App\Models\Project::first();
    $task = \App\Models\Task::first();
    $comment = \App\Models\Comment::first();

    if (!$admin) {
        echo "❌ Admin non trouvé\n";
        exit(1);
    }

    echo "👤 Admin: " . $admin->name . " (" . $admin->email . ")\n";
    echo "👤 User: " . ($user ? $user->name . " (" . $user->email . ")" : "Aucun autre utilisateur") . "\n\n";

    echo "=== PERMISSIONS ADMIN DU SITE ===\n";
    if ($team) {
        echo "🏢 Peut supprimer équipe: " . ($admin->canDeleteTeam($team) ? "✅ OUI" : "❌ NON") . "\n";
    }
    if ($project) {
        echo "📂 Peut supprimer projet: " . ($admin->canDeleteProject($project) ? "✅ OUI" : "❌ NON") . "\n";
    }
    if ($task) {
        echo "📝 Peut supprimer tâche: " . ($admin->canDeleteTask($task) ? "✅ OUI" : "❌ NON") . "\n";
    }
    if ($comment) {
        echo "💬 Peut supprimer commentaire: " . ($admin->canDeleteComment($comment) ? "✅ OUI" : "❌ NON") . "\n";
    }

    if ($user) {
        echo "\n=== PERMISSIONS UTILISATEUR NORMAL ===\n";
        if ($team) {
            echo "🏢 Peut supprimer équipe: " . ($user->canDeleteTeam($team) ? "✅ OUI" : "❌ NON") . "\n";
        }
        if ($project) {
            echo "📂 Peut supprimer projet: " . ($user->canDeleteProject($project) ? "✅ OUI" : "❌ NON") . "\n";
        }
        if ($task) {
            echo "📝 Peut supprimer tâche: " . ($user->canDeleteTask($task) ? "✅ OUI" : "❌ NON") . "\n";
        }
        if ($comment) {
            echo "💬 Peut supprimer commentaire: " . ($user->canDeleteComment($comment) ? "✅ OUI" : "❌ NON") . "\n";
        }
    }

    echo "\n=== TEST TEAM ADMIN ===\n";
    // Créer un admin d'équipe pour les tests
    $teamAdmin = \App\Models\User::where('email', '!=', 'root@teamtask.com')->skip(1)->first();
    if ($teamAdmin && $team) {
        // S'assurer qu'il soit admin de l'équipe
        $team->users()->syncWithoutDetaching([$teamAdmin->id => ['role' => 'admin']]);
        $teamAdmin->refresh();

        echo "👤 Team Admin: " . $teamAdmin->name . "\n";
        echo "🏢 Peut supprimer équipe: " . ($teamAdmin->canDeleteTeam($team) ? "✅ OUI" : "❌ NON") . "\n";
        if ($project) {
            echo "📂 Peut supprimer projet: " . ($teamAdmin->canDeleteProject($project) ? "✅ OUI" : "❌ NON") . "\n";
        }
        if ($task) {
            echo "📝 Peut supprimer tâche: " . ($teamAdmin->canDeleteTask($task) ? "✅ OUI" : "❌ NON") . "\n";
        }
        if ($comment) {
            echo "💬 Peut supprimer commentaire: " . ($teamAdmin->canDeleteComment($comment) ? "✅ OUI" : "❌ NON") . "\n";
        }
    }

    echo "\n✅ Tests terminés avec succès !\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
