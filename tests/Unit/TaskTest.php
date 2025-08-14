<?php

use App\Models\Task;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crée une tâche avec les champs requis', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'title' => 'Ma tâche',
        'priority' => 'medium',
        'status' => 'to_do',
    ]);

    expect($task->fresh())
        ->project_id->toBe($project->id)
        ->title->toBe('Ma tâche')
        ->priority->toBe('medium')
        ->status->toBe('to_do');
});

it('formatte la date due_date si présente', function () {
    $task = Task::factory()->create(['due_date' => now()->addDay()]);
    expect($task->due_date)->not->toBeNull();
});

