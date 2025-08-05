<div>
    <div class="container mx-auto px-4 py-8 text-white">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex items-center space-x-2 text-sm text-gray-400 mb-2">
                <a href="/dashboard" class="hover:text-white transition-colors">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="/projects/{{ $project->team_id }}" class="hover:text-white transition-colors">{{ $project->team->name }}</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white">{{ $project->name }}</span>
            </nav>
            <h1 class="text-3xl font-bold text-white">{{ $project->name }}</h1>
            @if($project->description)
                <p class="text-gray-300 mt-2">{{ $project->description }}</p>
            @endif
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Tasks Section -->
            <div class="bg-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">Tâches</h2>
                    <div class="flex items-center space-x-3">
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                            {{ $tasks->count() }} tâche{{ $tasks->count() > 1 ? 's' : '' }}
                        </span>
                        <button
                            wire:click="toggleCreateTaskForm"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 text-sm cursor-pointer">
                            {{ $showCreateTaskForm ? 'Annuler' : 'Nouvelle tâche' }}
                        </button>
                    </div>
                </div>

                <!-- Create Task Form -->
                @if($showCreateTaskForm)
                    <div class="bg-gray-700 border-2 border-gray-500 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Créer une nouvelle tâche</h3>
                        <form wire:submit.prevent="createTask">
                            <div class="mb-4">
                                <label for="newTaskTitle" class="block text-sm font-medium text-gray-300 mb-2">
                                    Titre de la tâche
                                </label>
                                <input
                                    type="text"
                                    id="newTaskTitle"
                                    wire:model="newTaskTitle"
                                    class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                                    placeholder="Entrez le titre de la tâche"
                                    required>
                                @error('newTaskTitle')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="newTaskDescription" class="block text-sm font-medium text-gray-300 mb-2">
                                    Description (optionnelle)
                                </label>
                                <textarea
                                    id="newTaskDescription"
                                    wire:model="newTaskDescription"
                                    rows="3"
                                    class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                                    placeholder="Décrivez la tâche..."></textarea>
                                @error('newTaskDescription')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="newTaskPriority" class="block text-sm font-medium text-gray-300 mb-2">
                                        Priorité
                                    </label>
                                    <select
                                        id="newTaskPriority"
                                        wire:model="newTaskPriority"
                                        class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:outline-none focus:border-blue-500">
                                        <option value="low">Basse</option>
                                        <option value="medium">Moyenne</option>
                                        <option value="high">Élevée</option>
                                    </select>
                                    @error('newTaskPriority')
                                        <span class="text-red-400 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="newTaskDueDate" class="block text-sm font-medium text-gray-300 mb-2">
                                        Date d'échéance (optionnelle)
                                    </label>
                                    <input
                                        type="date"
                                        id="newTaskDueDate"
                                        wire:model="newTaskDueDate"
                                        class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:outline-none focus:border-blue-500">
                                    @error('newTaskDueDate')
                                        <span class="text-red-400 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    Assignés (optionnel - par défaut assigné à vous)
                                </label>
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    @foreach($teamMembers as $member)
                                        <label class="flex items-center space-x-2 text-sm">
                                            <input
                                                type="checkbox"
                                                wire:model="selectedAssignees"
                                                value="{{ $member->id }}"
                                                class="rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500">
                                            <span class="text-gray-300">{{ $member->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedAssignees')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex gap-3">
                                <button
                                    type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                                    Créer la tâche
                                </button>
                                <button
                                    type="button"
                                    wire:click="toggleCreateTaskForm"
                                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="space-y-4">
                    @forelse($tasks as $task)
                        <div class="bg-gray-700 rounded-lg p-4 hover:bg-gray-650 transition-colors">
                            @if($editingTaskId === $task->id)
                                <!-- Edit Task Form -->
                                <form wire:submit.prevent="updateTask">
                                    <div class="mb-3">
                                        <input
                                            type="text"
                                            wire:model="editTaskTitle"
                                            class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded text-white text-lg font-semibold"
                                            required>
                                        @error('editTaskTitle')
                                            <span class="text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <textarea
                                            wire:model="editTaskDescription"
                                            rows="2"
                                            class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded text-white text-sm"
                                            placeholder="Description..."></textarea>
                                        @error('editTaskDescription')
                                            <span class="text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <select wire:model="editTaskStatus" class="px-3 py-1 bg-gray-600 border border-gray-500 rounded text-white text-xs">
                                            <option value="to_do">À faire</option>
                                            <option value="in_progress">En cours</option>
                                            <option value="done">Terminé</option>
                                            <option value="blocked">Bloqué</option>
                                        </select>
                                        @error('editTaskStatus')
                                            <span class="text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors cursor-pointer">
                                            Sauvegarder
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="cancelEditing"
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm transition-colors cursor-pointer">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            @else
                                <!-- Display Task -->
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-white">{{ $task->title }}</h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            {{ $task->status === 'done' ? 'bg-green-600 text-white' : '' }}
                                            {{ $task->status === 'in_progress' ? 'bg-yellow-600 text-white' : '' }}
                                            {{ $task->status === 'to_do' ? 'bg-gray-600 text-white' : '' }}
                                            {{ $task->status === 'blocked' ? 'bg-red-600 text-white' : '' }}">
                                            @if($task->status === 'to_do')
                                                À faire
                                            @elseif($task->status === 'in_progress')
                                                En cours
                                            @elseif($task->status === 'done')
                                                Terminé
                                            @elseif($task->status === 'blocked')
                                                Bloqué
                                            @endif
                                        </span>
                                        <button
                                            wire:click="startEditing({{ $task->id }})"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors cursor-pointer">
                                            Modifier
                                        </button>
                                    </div>
                                </div>
                                @if($task->description)
                                    <p class="text-gray-300 text-sm mb-3">{{ $task->description }}</p>
                                @endif
                                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                                    <div class="flex items-center space-x-4">
                                        <span class="px-2 py-1 rounded text-xs
                                            {{ $task->priority === 'high' ? 'bg-red-600 text-white' : '' }}
                                            {{ $task->priority === 'medium' ? 'bg-yellow-600 text-white' : '' }}
                                            {{ $task->priority === 'low' ? 'bg-green-600 text-white' : '' }}">
                                            Priorité:
                                            @if($task->priority === 'high')
                                                Élevée
                                            @elseif($task->priority === 'medium')
                                                Moyenne
                                            @else
                                                Basse
                                            @endif
                                        </span>
                                        @if($task->due_date)
                                            <span>Échéance: {{ $task->due_date->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                    <span>{{ $task->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <span>Assignés: </span>
                                    @if($task->assignedUsers->count() > 0)
                                        @foreach($task->assignedUsers as $user)
                                            <span class="inline-block bg-blue-600 text-white px-2 py-1 rounded mr-1 mb-1">{{ $user->name }}</span>
                                        @endforeach
                                    @elseif($task->assigned)
                                        <span class="inline-block bg-blue-600 text-white px-2 py-1 rounded mr-1 mb-1">{{ $task->assigned->name }}</span>
                                    @else
                                        <span>Non assigné</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-8 w-8 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-300 mb-2">Aucune tâche</h3>
                            <p class="text-gray-500">Ce projet n'a pas encore de tâches.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">Commentaires</h2>
                    <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-sm">
                        {{ $comments->count() }} commentaire{{ $comments->count() > 1 ? 's' : '' }}
                    </span>
                </div>

                <!-- Add Comment Form -->
                <div class="bg-gray-700 border-2 border-gray-500 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-3">Ajouter un commentaire</h3>
                    <form wire:submit.prevent="addComment">
                        <div class="mb-3">
                            <textarea
                                wire:model="newComment"
                                rows="3"
                                class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                                placeholder="Écrivez votre commentaire..."
                                required></textarea>
                            @error('newComment')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <button
                            type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                            Publier commentaire
                        </button>
                    </form>
                </div>

                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($comments as $comment)
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-white">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-gray-300 text-sm">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-8 w-8 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-300 mb-2">Aucun commentaire</h3>
                            <p class="text-gray-500">Ce projet n'a pas encore de commentaires.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
