<div>
    <div class="container mx-auto px-4 py-8 text-slate-800 dark:text-white bg-slate-100 dark:bg-transparent">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <nav class="flex items-center space-x-2 text-sm text-slate-600 dark:text-gray-400">
                    <a href="/dashboard" class="hover:text-slate-800 dark:hover:text-white transition-colors">Dashboard</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="/projects/{{ $project->team_id }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">{{ $project->team->name }}</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-slate-800 dark:text-white">{{ $project->name }}</span>
                </nav>
                <a href="/projects/{{ $project->team_id }}"
                   class="bg-slate-200 dark:bg-gray-800 hover:bg-slate-300 dark:hover:bg-gray-700 text-slate-800 dark:text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 shadow-lg flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Projets</span>
                </a>
            </div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $project->name }}</h1>
            @if($project->description)
                <p class="text-slate-600 dark:text-gray-300 mt-2">{{ $project->description }}</p>
            @endif
        </div>

        <livewire:ui.flash-message />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Tasks Section -->
            <div class="bg-slate-50 dark:bg-gray-800 border border-slate-200 dark:border-gray-600 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Tâches</h2>
                    <div class="flex items-center space-x-3">
                        <span class="bg-emerald-600 dark:bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                            {{ $tasks->count() }} tâche{{ $tasks->count() > 1 ? 's' : '' }}
                        </span>
                        <button
                            wire:click="toggleCreateTaskForm"
                            class="bg-emerald-600 dark:bg-green-600 hover:bg-emerald-700 dark:hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 text-sm cursor-pointer">
                            {{ $showCreateTaskForm ? 'Annuler' : 'Nouvelle tâche' }}
                        </button>
                    </div>
                </div>

                <!-- Create Task Form -->
                @if($showCreateTaskForm)
                    <div class="bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-500 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Créer une nouvelle tâche</h3>
                        <form wire:submit.prevent="createTask">
                            <div class="mb-4">
                                <label for="newTaskTitle" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                                    Titre de la tâche
                                </label>
                                <input
                                    type="text"
                                    id="newTaskTitle"
                                    wire:model="newTaskTitle"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-slate-300 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white placeholder-slate-500 dark:placeholder-gray-400 focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                                    placeholder="Entrez le titre de la tâche"
                                    required>
                                @error('newTaskTitle')
                                    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="newTaskDescription" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                                    Description (optionnelle)
                                </label>
                                <textarea
                                    id="newTaskDescription"
                                    wire:model="newTaskDescription"
                                    rows="3"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-slate-300 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white placeholder-slate-500 dark:placeholder-gray-400 focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                                    placeholder="Décrivez la tâche..."></textarea>
                                @error('newTaskDescription')
                                    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="newTaskPriority" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                                        Priorité
                                    </label>
                                    <select
                                        id="newTaskPriority"
                                        wire:model="newTaskPriority"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-slate-300 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                                        <option value="low">Basse</option>
                                        <option value="medium">Moyenne</option>
                                        <option value="high">Élevée</option>
                                    </select>
                                    @error('newTaskPriority')
                                        <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="newTaskDueDate" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                                        Date d'échéance (optionnelle)
                                    </label>
                                    <input
                                        type="date"
                                        id="newTaskDueDate"
                                        wire:model="newTaskDueDate"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-slate-300 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                                    @error('newTaskDueDate')
                                        <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                                    Assignés (optionnel - par défaut assigné à vous)
                                </label>
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    @foreach($teamMembers as $member)
                                        <label class="flex items-center space-x-2 text-sm">
                                            <input
                                                type="checkbox"
                                                wire:model="selectedAssignees"
                                                value="{{ $member->id }}"
                                                class="rounded bg-white dark:bg-gray-600 border-slate-300 dark:border-gray-500 text-emerald-600 dark:text-blue-600 focus:ring-emerald-500 dark:focus:ring-blue-500">
                                            <span class="text-slate-700 dark:text-gray-300">{{ $member->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedAssignees')
                                    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex gap-3">
                                <button
                                    type="submit"
                                    class="bg-emerald-600 dark:bg-green-600 hover:bg-emerald-700 dark:hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                                    Créer la tâche
                                </button>
                                <button
                                    type="button"
                                    wire:click="toggleCreateTaskForm"
                                    class="bg-slate-400 dark:bg-gray-600 hover:bg-slate-500 dark:hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Filters & Sorting -->
                    <div class="bg-white border-2 border-slate-200 dark:bg-gray-700 dark:border-gray-600 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Statut -->
                        <div>
                        <label class="block text-xs uppercase tracking-wider text-slate-600 dark:text-gray-400 mb-2">Statut</label>
                        <select wire:model.change="filterStatus"
                                class="w-full px-3 py-2 bg-white border border-slate-300 dark:bg-gray-600 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                            <option value="all">Tous</option>
                            <option value="to_do">À faire</option>
                            <option value="in_progress">En cours</option>
                            <option value="done">Terminé</option>
                            <option value="blocked">Bloqué</option>
                        </select>
                        </div>

                        <!-- Assigné -->
                        <div>
                        <label class="block text-xs uppercase tracking-wider text-slate-600 dark:text-gray-400 mb-2">Assigné</label>
                        <select wire:model.change="filterAssignee"
                                class="w-full px-3 py-2 bg-white border border-slate-300 dark:bg-gray-600 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                            <option value="all">Tous</option>
                            @foreach($teamMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                        </div>

                        <!-- Date d'échéance - de -->
                        <div>
                        <label class="block text-xs uppercase tracking-wider text-slate-600 dark:text-gray-400 mb-2">Échéance min</label>
                        <input type="date" wire:model.change="filterDateFrom"
                                class="w-full px-3 py-2 bg-white border border-slate-300 dark:bg-gray-600 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                        </div>

                        <!-- Date d'échéance - à -->
                        <div>
                        <label class="block text-xs uppercase tracking-wider text-slate-600 dark:text-gray-400 mb-2">Échéance max</label>
                        <input type="date" wire:model.change="filterDateTo"
                                class="w-full px-3 py-2 bg-white border border-slate-300 dark:bg-gray-600 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <!-- Tri -->
                        <div class="flex items-center gap-2">
                        <span class="text-xs uppercase tracking-wider text-slate-600 dark:text-gray-400">Trier par:</span>

                        <button type="button" wire:click="setSort('created_at')"
                                class="px-3 py-1 rounded-lg text-sm
                                {{ $sortField==='created_at' ? 'bg-emerald-600 text-white dark:bg-blue-600' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500' }}">
                            Date de création
                            @if($sortField==='created_at')
                            <span class="ml-1 text-xs opacity-80">{{ $sortDir==='asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>

                        <button type="button" wire:click="setSort('priority')"
                                class="px-3 py-1 rounded-lg text-sm
                                {{ $sortField==='priority' ? 'bg-emerald-600 text-white dark:bg-blue-600' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500' }}">
                            Priorité
                            @if($sortField==='priority')
                            <span class="ml-1 text-xs opacity-80">{{ $sortDir==='asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                        </div>

                        <!-- Reset rapide (optionnel) -->
                        <button type="button"
                                wire:click="resetFilters"
                                class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-gray-600 dark:hover:bg-gray-700 dark:text-white px-3 py-1 rounded-lg">
                        Réinitialiser
                        </button>
                    </div>
                    </div>

                    @forelse($tasks as $task)
                        <div class="bg-white border border-slate-200 dark:bg-gray-700 dark:border-gray-600 rounded-lg p-4 hover:bg-slate-50 dark:hover:bg-gray-600 transition-colors">
                            @if($editingTaskId === $task->id)
                                <!-- Edit Task Form -->
                                <form wire:submit.prevent="updateTask">
                                    <div class="mb-3">
                                        <input
                                            type="text"
                                            wire:model="editTaskTitle"
                                            class="w-full px-3 py-2 bg-white border border-slate-300 dark:bg-gray-600 dark:border-gray-500 rounded text-slate-800 dark:text-white text-lg font-semibold focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                                            required>
                                        @error('editTaskTitle')
                                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <textarea
                                            wire:model="editTaskDescription"
                                            rows="2"
                                            class="w-full px-3 py-2 bg-white border border-slate-300 dark:bg-gray-600 dark:border-gray-500 rounded text-slate-800 dark:text-white text-sm focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                                            placeholder="Description..."></textarea>
                                        @error('editTaskDescription')
                                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <select wire:model.change="editTaskStatus" class="px-3 py-1 bg-white border border-slate-300 dark:bg-gray-600 dark:border-gray-500 rounded text-slate-800 dark:text-white text-xs focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                                            <option value="to_do">À faire</option>
                                            <option value="in_progress">En cours</option>
                                            <option value="done">Terminé</option>
                                            <option value="blocked">Bloqué</option>
                                        </select>
                                        @error('editTaskStatus')
                                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-700 dark:bg-green-600 dark:hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors cursor-pointer">
                                            Sauvegarder
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="cancelEditing"
                                            class="bg-slate-400 hover:bg-slate-500 dark:bg-gray-600 dark:hover:bg-gray-700 text-white px-3 py-1 rounded text-sm transition-colors cursor-pointer">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            @else
                                <!-- Display Task -->
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">{{ $task->title }}</h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            {{ $task->status === 'done' ? 'bg-emerald-600 text-white dark:bg-green-600' : '' }}
                                            {{ $task->status === 'in_progress' ? 'bg-amber-500 text-white dark:bg-yellow-600' : '' }}
                                            {{ $task->status === 'to_do' ? 'bg-slate-400 text-white dark:bg-gray-600' : '' }}
                                            {{ $task->status === 'blocked' ? 'bg-red-500 text-white dark:bg-red-600' : '' }}">
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
                                            class="bg-emerald-600 hover:bg-emerald-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors cursor-pointer">
                                            Modifier
                                        </button>
                                        @can('deleteTask', $task)
                                            <button
                                                wire:click="deleteTask({{ $task->id }})"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ? Cette action est irréversible.')"
                                                class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors cursor-pointer">
                                                Supprimer
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                                @if($task->description)
                                    <p class="text-slate-600 dark:text-gray-300 text-sm mb-3">{{ $task->description }}</p>
                                @endif
                                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-gray-400 mb-2">
                                    <div class="flex items-center space-x-4">
                                        <span class="px-2 py-1 rounded text-xs
                                            {{ $task->priority === 'high' ? 'bg-red-500 text-white dark:bg-red-600' : '' }}
                                            {{ $task->priority === 'medium' ? 'bg-amber-500 text-white dark:bg-yellow-600' : '' }}
                                            {{ $task->priority === 'low' ? 'bg-emerald-500 text-white dark:bg-green-600' : '' }}">
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
                                <div class="text-xs text-slate-500 dark:text-gray-400">
                                    <span>Assignés: </span>
                                    @if($task->assignedUsers->count() > 0)
                                        @foreach($task->assignedUsers as $user)
                                            <span class="inline-block bg-emerald-600 dark:bg-blue-600 text-white px-2 py-1 rounded mr-1 mb-1">{{ $user->name }}</span>
                                        @endforeach
                                    @elseif($task->assigned)
                                        <span class="inline-block bg-emerald-600 dark:bg-blue-600 text-white px-2 py-1 rounded mr-1 mb-1">{{ $task->assigned->name }}</span>
                                    @else
                                        <span>Non assigné</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-8 w-8 text-slate-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-slate-600 dark:text-gray-300 mb-2">Aucune tâche</h3>
                            <p class="text-slate-500 dark:text-gray-500">Ce projet n'a pas encore de tâches.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white border border-slate-200 dark:bg-gray-800 dark:border-gray-700 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Commentaires</h2>
                    <span class="bg-emerald-600 dark:bg-purple-600 text-white px-3 py-1 rounded-full text-sm">
                        {{ $comments->count() }} commentaire{{ $comments->count() > 1 ? 's' : '' }}
                    </span>
                </div>

                <!-- Add Comment Form -->
                <div class="bg-slate-50 border-2 border-slate-200 dark:bg-gray-700 dark:border-gray-500 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Ajouter un commentaire</h3>
                    <form wire:submit.prevent="addGlobalComment">
                        <div class="mb-3">
                            <textarea
                                wire:model="newComment"
                                rows="3"
                                class="w-full px-3 py-2 bg-white border border-slate-300 dark:bg-gray-600 dark:border-gray-500 rounded-lg text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                                placeholder="Écrivez votre commentaire..."
                                required></textarea>
                            @error('newComment')
                                <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <button
                            type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 dark:bg-purple-600 dark:hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                            Publier commentaire
                        </button>
                    </form>
                </div>
                <!-- Comments list -->
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($comments as $comment)
                        <div class="bg-slate-50 border border-slate-200 dark:bg-gray-700 dark:border-gray-600 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-slate-800 dark:text-white">{{ $comment->user->name }}</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-slate-500 dark:text-gray-400">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                    @can('deleteComment', $comment)
                                        <button
                                            wire:click="deleteComment({{ $comment->id }})"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs"
                                            title="Supprimer le commentaire"
                                        >
                                            🗑️
                                        </button>
                                    @endcan
                                </div>
                            </div>
                            <p class="text-slate-600 dark:text-gray-300 text-sm">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-8 w-8 text-slate-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-slate-600 dark:text-gray-300 mb-2">Aucun commentaire</h3>
                            <p class="text-slate-500 dark:text-gray-500">Ce projet n'a pas encore de commentaires.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
