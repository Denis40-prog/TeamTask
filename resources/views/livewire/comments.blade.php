<!-- Commentaires -->
<div class="bg-gray-800 text-gray-400 rounded shadow-md p-6">
    <h2 class="text-2xl font-semibold mb-4">Commentaires</h2>

    <!-- Liste des commentaires -->
    <ul class="mb-4">
        @forelse ($comments as $comment)
            <li class="border-b border-gray-200 pb-2 mb-2">
                <strong class="text-white">{{ $comment->user->name }}</strong> :
                <span class="text-white">{{ $comment->content }}</span>
            </li>
        @empty
            <li class="text-gray-500">Aucun commentaire pour ce projet.</li>
        @endforelse
        {{ $comments->links() }}
    </ul>

    <!-- Formulaire pour ajouter un commentaire -->
    <div>
        <label for="task_id" class="block text-white">Lier une tâche (optionnel) :</label>
        <select name="task_id" wire:model="taskId" id="task_id" class="w-full border-gray-300 rounded p-2">
            <option value="">Sélectionner une tâche</option>
            @foreach ($project->tasks as $task)
                <option value="{{ $task->id }}">{{ $task->title }}</option>
            @endforeach
        </select>
    </div>
    <div>
        @error('content') <span class="error">{{ $message }}</span> @enderror
        <label for="content" class="block text-white">Votre commentaire :</label>
        <textarea name="content" wire:model="content" id="content" class="w-full border-gray-300 rounded p-2" rows="3"
            required></textarea>
    </div>
    <button wire:click="createComment" type="submit"
        class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Ajouter</button>
</div>
