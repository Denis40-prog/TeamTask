<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function store(Request $request) {
        $data = Validator::make($request->all(), [
            'content' => ['required'],
            'project_id' => ['required', 'exists:projects,id'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if ($data->fails()) {
            throw ValidationException::withMessages($data->errors()->toArray());
        }

        // Création de la tâche
        $content = Comment::insert([
            'content' => $request->content,
            'project_id' => $request->project_id,
            'task_id' => $request->task_id,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('project.show', ['project' => $request->project_id])->with('success', 'Comment added successfully');
    }
}
