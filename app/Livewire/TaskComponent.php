<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Project;
use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TaskComponent extends Component
{
    public $projectId;
    public $project;

    // Création de tâches
    public $showCreateTaskForm = false;
    public $newTaskTitle = '';
    public $newTaskDescription = '';

    // Création de commentaires
    public $newComment = '';

    // Édition de tâches
    public $editingTaskId = null;
    public $editTaskTitle = '';
    public $editTaskDescription = '';
    public $editTaskStatus = '';

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->project = Project::with('team')->findOrFail($projectId);

        // Vérifier que l'utilisateur fait partie de l'équipe
        if (!$this->project->team->users->contains(Auth::id())) {
            abort(403, 'Vous n\'avez pas accès à ce projet.');
        }
    }

    public function toggleCreateTaskForm()
    {
        $this->showCreateTaskForm = !$this->showCreateTaskForm;
        $this->reset(['newTaskTitle', 'newTaskDescription']);
    }

    public function createTask()
    {
        $this->validate([
            'newTaskTitle' => 'required|string|max:255',
            'newTaskDescription' => 'nullable|string|max:1000',
        ]);

        Task::create([
            'title' => $this->newTaskTitle,
            'description' => $this->newTaskDescription,
            'project_id' => $this->projectId,
            'assigned_id' => Auth::id(),
            'status' => 'to_do',
        ]);

        $this->reset(['newTaskTitle', 'newTaskDescription', 'showCreateTaskForm']);
        session()->flash('message', 'Tâche créée avec succès !');
    }

    public function startEditing($taskId)
    {
        $task = Task::findOrFail($taskId);
        $this->editingTaskId = $taskId;
        $this->editTaskTitle = $task->title;
        $this->editTaskDescription = $task->description;
        $this->editTaskStatus = $task->status;
    }

    public function updateTask()
    {
        $this->validate([
            'editTaskTitle' => 'required|string|max:255',
            'editTaskDescription' => 'nullable|string|max:1000',
            'editTaskStatus' => 'required|in:to_do,in_progress,done,blocked',
        ]);

        $task = Task::findOrFail($this->editingTaskId);
        $task->update([
            'title' => $this->editTaskTitle,
            'description' => $this->editTaskDescription,
            'status' => $this->editTaskStatus,
        ]);

        $this->reset(['editingTaskId', 'editTaskTitle', 'editTaskDescription', 'editTaskStatus']);
        session()->flash('message', 'Tâche mise à jour avec succès !');
    }

    public function cancelEditing()
    {
        $this->reset(['editingTaskId', 'editTaskTitle', 'editTaskDescription', 'editTaskStatus']);
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        // Créer un commentaire général pour le projet (associé à la première tâche ou créer une tâche système)
        $firstTask = Task::where('project_id', $this->projectId)->first();

        if (!$firstTask) {
            // S'il n'y a pas de tâches, créer une tâche système pour les commentaires généraux
            $firstTask = Task::create([
                'title' => 'Commentaires généraux',
                'description' => 'Tâche système pour les commentaires généraux du projet',
                'project_id' => $this->projectId,
                'assigned_user_id' => Auth::id(),
                'status' => 'completed',
                'created_by' => Auth::id(),
            ]);
        }

        Comment::create([
            'content' => $this->newComment,
            'task_id' => $firstTask->id,
            'user_id' => Auth::id(),
        ]);

        $this->reset(['newComment']);
        session()->flash('message', 'Commentaire ajouté avec succès !');
    }

    public function render()
    {
        $tasks = Task::where('project_id', $this->projectId)->get();

        // Récupérer les commentaires liés aux tâches de ce projet
        $taskIds = $tasks->pluck('id');
        $comments = Comment::whereIn('task_id', $taskIds)
                          ->with('user')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return view('livewire.task-component', [
            'tasks' => $tasks,
            'comments' => $comments
        ]);
    }
}
