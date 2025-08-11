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
    public $newTaskPriority = 'medium';
    public $newTaskDueDate = '';
    public $selectedAssignees = [];

    // Création de commentaires
    public $newComment = '';

    // Édition de tâches
    public $editingTaskId = null;
    public $editTaskTitle = '';
    public $editTaskDescription = '';
    public $editTaskStatus = '';
    // Filtres
    public $filterStatus = 'all';
    public $filterAssignee = 'all';
    public $filterDateFrom = null;
    public $filterDateTo = null;

    // Tri
    public $sortField = 'created_at'; // 'created_at' | 'priority'
    public $sortDir = 'desc'; // 'asc' | 'desc'

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
        $this->reset(['newTaskTitle', 'newTaskDescription', 'newTaskPriority', 'newTaskDueDate', 'selectedAssignees']);
    }

    public function createTask()
    {
        $this->validate([
            'newTaskTitle' => 'required|string|max:255',
            'newTaskDescription' => 'nullable|string|max:1000',
            'newTaskPriority' => 'required|in:low,medium,high',
            'newTaskDueDate' => 'nullable|date',
            'selectedAssignees' => 'nullable|array',
            'selectedAssignees.*' => 'exists:users,id',
        ]);

        $task = Task::create([
            'title' => $this->newTaskTitle,
            'description' => $this->newTaskDescription,
            'priority' => $this->newTaskPriority,
            'due_date' => $this->newTaskDueDate ?: null,
            'project_id' => $this->projectId,
            'assigned_id' => Auth::id(),
            'status' => 'to_do',
        ]);

        // Assign multiple users if selected, otherwise assign to creator
        if (!empty($this->selectedAssignees)) {
            $task->assignedUsers()->attach($this->selectedAssignees);
        } else {
            $task->assignedUsers()->attach(Auth::id());
        }

        $this->reset(['newTaskTitle', 'newTaskDescription', 'newTaskPriority', 'newTaskDueDate', 'selectedAssignees', 'showCreateTaskForm']);
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

    public function setSort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = $field === 'priority' ? 'desc' : 'desc';
        }
    }

    public function resetFilters()
    {
        $this->filterStatus   = 'all';
        $this->filterAssignee = 'all';
        $this->filterDateFrom = null;
        $this->filterDateTo   = null;
        $this->sortField      = 'created_at';
        $this->sortDir        = 'desc';
    }

    public function render()
    {
        $teamMembers = $this->project->team->users;

        $query = Task::where('project_id', $this->projectId)
            ->with(['assignedUsers', 'assigned']);

        // --- Filtres ---
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterAssignee !== 'all') {
            $query->whereHas('assignedUsers', function ($q) {
                $q->where('users.id', $this->filterAssignee);
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('due_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('due_date', '<=', $this->filterDateTo);
        }

        // --- Tri ---
        if ($this->sortField === 'priority') {
            // Tri custom par priorité
            if ($this->sortDir === 'asc') {
                $query->orderByRaw("FIELD(priority, 'low','medium','high')");
            } else {
                $query->orderByRaw("FIELD(priority, 'high','medium','low')");
            }
        } else {
            // created_at (par défaut)
            $query->orderBy($this->sortField, $this->sortDir);
        }

        $tasks = $query->get();

        // Commentaires
        $taskIds = $tasks->pluck('id');
        $comments = Comment::whereIn('task_id', $taskIds)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.task-component', [
            'tasks' => $tasks,
            'comments' => $comments,
            'teamMembers' => $teamMembers
        ]);
    }

}
