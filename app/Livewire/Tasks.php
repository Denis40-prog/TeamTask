<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class Tasks extends Component
{
    use WithPagination;

    public $project;

    #[Validate('required')]
    public string $title = '';
    #[Validate('required')]
    public string $description = '';
    #[Validate('required')]
    public string $status = 'À faire';
    #[Validate('required')]
    public string $priority = 'Moyenne';
    #[Validate('required')]
    public ?int $assigneeId = null;
    public ?int $dueDate = null;

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function render()
    {
        $tasks = $this->project->tasks()
            ->select(['title', 'description', 'status', 'priority', 'due_date', 'assignee_id'])
            ->with('assignee:id,name')
            ->paginate(5);

        return view('livewire.tasks', [
            'tasks' => $tasks
        ]);
    }

    public function createTask()
    {
        $this->validate();

        Task::insert([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status ?: 'À faire',
            'priority' => $this->priority ?: 'Moyenne',
            'due_date' => $this->dueDate,
            'assignee_id' => $this->assigneeId,
            'project_id' => $this->project->id,
        ]);

        $this->reset('title', 'description', 'status', 'priority', 'dueDate', 'assigneeId');
    }
}
