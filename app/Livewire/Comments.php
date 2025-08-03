<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use App\Models\Comment;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class Comments extends Component
{
    use WithPagination;

    public $project;

    #[Validate('required')]
    public string $content = '';
    public ?int $taskId = null;

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function render()
    {
        $comments = $this->project->comments()
            ->select(['content', 'user_id'])
            ->with('user:id,name')
            ->paginate(5);

        return view('livewire.comments', [
            'comments' => $comments
        ]);
    }

    public function createComment()
    {
        $this->validate();

        Comment::insert([
            'content' => $this->content,
            'project_id' => $this->project->id,
            'task_id' => $this->taskId,
            'user_id' => auth()->id(),
        ]);

        $this->reset('content', 'taskId');
    }
}
