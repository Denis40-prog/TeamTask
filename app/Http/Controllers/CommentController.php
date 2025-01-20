<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Comment $comment) {
        return "store comment {{ $comment }}";
    }
}
