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

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::resource('users', UserController::class);
    Route::resource('teams', TeamController::class);
    // Route::resource('projects', ProjectController::class); // Migré vers Livewire
    Route::resource('tasks', TaskController::class);
    Route::resource('comments', CommentController::class);
    Route::resource('badges', BadgeController::class);
    Route::resource('user-badges', UserBadgeController::class);
    Route::resource('user-xp', UserXpController::class);
    Route::resource('notifications', NotificationController::class);
    Route::resource('team-users', TeamUserController::class);
    Route::resource('challenges', ChallengeController::class);
    Route::resource('challenge-participants', ChallengeParticipantController::class);
    Route::resource('wellness-surveys', WellnessSurveyController::class);

    // Livewire
    Route::get('/livewire/users', UserComponent::class)->name('livewire.users');
    Route::get('/livewire/teams', TeamComponent::class)->name('livewire.teams');
    Route::get('/livewire/projects/{teamId?}', ProjectComponent::class)->name('livewire.projects');
    Route::get('/livewire/tasks', TaskComponent::class)->name('livewire.tasks');
    Route::get('/livewire/comments', CommentComponent::class)->name('livewire.comments');
    Route::get('/livewire/badges', BadgeComponent::class)->name('livewire.badges');
    Route::get('/livewire/user-badges', UserBadgeComponent::class)->name('livewire.user-badges');
    Route::get('/livewire/user-xp', UserXpComponent::class)->name('livewire.user-xp');
    Route::get('/livewire/notifications', NotificationComponent::class)->name('livewire.notifications');
    Route::get('/livewire/team-users', TeamUserComponent::class)->name('livewire.team-users');
    Route::get('/livewire/challenges', ChallengeComponent::class)->name('livewire.challenges');
    Route::get('/livewire/challenge-participants', ChallengeParticipantComponent::class)->name('livewire.challenge-participants');
    Route::get('/livewire/wellness-surveys', WellnessSurveyComponent::class)->name('livewire.wellness-surveys');
});

require __DIR__.'/auth.php';
