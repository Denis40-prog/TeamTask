<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function edit(Task $task) {
        return "edit de la task {{ $task }}";
    }

    public function destroy(Task $task) {
        return "destroy task {{ $task }}";
    }

    public function store(Request $request) {
        $data = Validator::make($request->all(), [
            'title' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
            'priority' => ['required'],
            'assignee_id' => ['required', 'exists:users,id'],
            'project_id' => ['required', 'exists:projects,id'],
        ]);

        if ($data->fails()) {
            throw ValidationException::withMessages($data->errors()->toArray());
        }

        // Création de la tâche
        $task = Task::insert([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'priority' => $request->priority,
            'assignee_id' => $request->assignee_id,
            'project_id' => $request->project_id,
        ]);
        // Ajout d'un log pour vérifier que la tâche est bien créée

        return $task;
    }
}
