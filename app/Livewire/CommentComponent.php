<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentComponent extends Component
{
    public $comment;

    public function mount(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function deleteComment()
    {
        // Vérifier les permissions
        if (!Gate::allows('deleteComment', $this->comment)) {
            $this->dispatch('flash', type: 'error', text: 'Vous n\'avez pas les permissions pour supprimer ce commentaire.');
            return;
        }

        try {
            $this->comment->delete();
            $this->dispatch('flash', type: 'success', text: 'Le commentaire a été supprimé avec succès.');
            $this->dispatch('commentDeleted');
        } catch (\Exception $e) {
            $this->dispatch('flash', type: 'error', text: 'Une erreur est survenue lors de la suppression du commentaire.');
        }
    }

    public function canDelete()
    {
        return Gate::allows('deleteComment', $this->comment);
    }

    public function render()
    {
        return view('livewire.comment-component');
    }
}
