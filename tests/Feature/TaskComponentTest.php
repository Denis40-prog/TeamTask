<?php

use App\Livewire\TaskComponent;
use App\Models\User;
use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function bootProjectWithUser(): array {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    if (method_exists($team, 'users')) {
        $team->users()->attach($user->id);
    }
    $project = Project::factory()->create(['team_id' => $team->id]);
    return compact('user','team','project');
}

it('affiche les tâches du projet', function () {
    ['user'=>$user, 'project'=>$project] = bootProjectWithUser();

    Task::factory()->count(3)->create(['project_id' => $project->id]);

    $this->actingAs($user);
    Livewire::test(TaskComponent::class, ['projectId' => $project->id])
        ->assertStatus(200)
        ->assertSee($project->name);
});

it('crée une tâche via le composant', function () {
    ['user'=>$user, 'project'=>$project] = bootProjectWithUser();

    $this->actingAs($user);
    Livewire::test(TaskComponent::class, ['projectId' => $project->id])
        ->set('showCreateTaskForm', true)
        ->set('newTaskTitle', 'Nouvelle tâche')
        ->set('newTaskDescription', 'Desc')
        ->set('newTaskPriority', 'high')
        ->call('createTask')
        ->assertSet('showCreateTaskForm', false) // le form s’est refermé
        ->assertSet('newTaskTitle', '')          // champs reset
        ->assertSet('newTaskDescription', '');   // champs reset

    expect(Task::where('project_id', $project->id)->count())->toBe(1);
});

it('met à jour une tâche via le composant', function () {
    ['user'=>$user, 'project'=>$project] = bootProjectWithUser();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'title' => 'Ancien titre',
        'status' => 'to_do',
    ]);

    $this->actingAs($user);
    Livewire::test(TaskComponent::class, ['projectId' => $project->id])
        ->call('startEditing', $task->id)
        ->set('editTaskTitle', 'Nouveau titre')
        ->set('editTaskDescription', 'New desc')
        ->set('editTaskStatus', 'in_progress')
        ->call('updateTask')
        ->assertSet('editingTaskId', null); // édition terminée

    $task->refresh();
    expect($task->title)->toBe('Nouveau titre')
        ->and($task->status)->toBe('in_progress');
});

it('filtre par statut et tri par priorité', function () {
    ['user'=>$user, 'project'=>$project] = bootProjectWithUser();

    Task::factory()->create(['project_id' => $project->id, 'title'=>'T1', 'status'=>'to_do', 'priority'=>'low']);
    Task::factory()->create(['project_id' => $project->id, 'title'=>'T2', 'status'=>'done', 'priority'=>'high']);
    Task::factory()->create(['project_id' => $project->id, 'title'=>'T3', 'status'=>'done', 'priority'=>'medium']);

    $this->actingAs($user);
    Livewire::test(TaskComponent::class, ['projectId' => $project->id])
        ->set('filterStatus', 'done')
        ->set('sortField', 'priority')
        ->set('sortDir', 'desc')
        ->assertSee('T2') // high
        ->assertSee('T3') // medium
        ->assertDontSee('T1'); // to_do
});
