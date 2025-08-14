<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\{
    UserController,
    TeamController,
    ProjectController,
    TaskController,
    CommentController,
    BadgeController,
    UserBadgeController,
    UserXpController,
    NotificationController,
    TeamUserController,
    ChallengeController,
    ChallengeParticipantController,
    WellnessSurveyController
};
use App\Livewire\{
    UserComponent,
    TeamComponent,
    ProjectComponent,
    TaskComponent,
    CommentComponent,
    BadgeComponent,
    UserBadgeComponent,
    UserXpComponent,
    NotificationComponent,
    TeamUserComponent,
    ChallengeComponent,
    ChallengeParticipantComponent,
    WellnessSurveyComponent
};

use App\Livewire\Wellness\TeamsList;
use App\Livewire\Wellness\TeamDashboard;
use App\Livewire\Wellness\MemberDetail;
use Illuminate\Support\Facades\Gate;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Routes des ressources traditionnelles
    Route::resource('comments', CommentController::class);
    Route::resource('badges', BadgeController::class);
    Route::resource('user-badges', UserBadgeController::class);
    Route::resource('user-xp', UserXpController::class);
    Route::resource('notifications', NotificationController::class);
    Route::resource('team-users', TeamUserController::class);
    Route::resource('challenges', ChallengeController::class);
    Route::resource('challenge-participants', ChallengeParticipantController::class);
    Route::resource('wellness-surveys', WellnessSurveyController::class);

    // Routes principales Livewire
    Route::get('/projects/{teamId}', ProjectComponent::class)->name('projects.index');
    Route::get('/projects/{projectId}/tasks', TaskComponent::class)->name('projects.tasks');
});

// wellness survey (formulaire membre)
Route::middleware(['auth', 'verified', 'team.member'])
    ->get('/wellness', WellnessSurveyComponent::class)
    ->name('wellness.survey');

// suivi admin
Route::middleware(['auth','verified'])->group(function () {
    // Liste des équipes (admin)
    Route::get('/wellness/suivi', TeamsList::class)
        ->middleware('can:viewWellness')
        ->name('wellness.followup');

    // Dashboard d'une équipe
    Route::get('/wellness/suivi/team/{team}', TeamDashboard::class)
        ->middleware('can:viewWellnessForTeam,team')
        ->name('wellness.followup.team');

    // Détails d'un membre
    Route::get('/wellness/suivi/team/{team}/member/{user}', MemberDetail::class)
        ->middleware('can:viewWellnessForTeam,team')
        ->name('wellness.followup.team.member');
});

require __DIR__.'/auth.php';
