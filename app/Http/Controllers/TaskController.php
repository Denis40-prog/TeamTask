<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function edit(Task $task) {
        return "edit de la task {{ $task }}";
    }

    public function destroy(Task $task) {
        return "destroy task {{ $task }}";
    }

    public function store(Task $task) {
        $data = $task->validate([
            'title' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
            'priority' => ['required'],
            'due_date' => ['required', 'numeric'],
            'assignee_id' => ['required', 'exists:users'],
        ]);

        return task::create($data);
    }
}
