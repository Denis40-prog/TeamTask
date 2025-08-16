<div>
    <div class="container mx-auto px-4 py-8 text-slate-800 dark:text-white bg-slate-100 dark:bg-transparent">
        <!-- Header -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-4">
                <nav class="flex items-center space-x-2 text-sm text-slate-600 dark:text-gray-400">
                    <a href="/dashboard" class="hover:text-slate-800 dark:hover:text-white transition-colors">Dashboard</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-slate-800 dark:text-white">{{ $team->name }}</span>
                </nav>
                <a href="/dashboard"
                   class="bg-slate-200 dark:bg-gray-800 hover:bg-slate-300 dark:hover:bg-gray-700 text-slate-800 dark:text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 shadow-lg flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>

            <h1 class="text-3xl font-bold text-slate-800 dark:text-white mb-4">Projets de {{ $team->name }}</h1>

            <div x-data="{ open: false }" class="w-full mb-2">
                <button
                    @click="open = !open"
                    class="w-full bg-slate-200 dark:bg-gray-800 text-left text-slate-800 dark:text-white font-semibold py-3 px-4 rounded-lg flex justify-between items-center hover:bg-slate-300 dark:hover:bg-gray-700 transition"
                >
                    <span>Membres de l'équipe ({{ $team->users->count() }})</span>
                    <svg :class="{'transform rotate-180': open}" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- liste des membres -->
                <div x-show="open" x-transition class="mt-4 bg-slate-50 dark:bg-gray-800 rounded-lg p-4 shadow-inner">
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Liste des membres</h2>
                    <ul class="mb-4">
                        @foreach($team->users as $member)
                            <li class="flex items-center justify-between bg-slate-100 dark:bg-gray-700 p-2 rounded mb-2">
                            <span>
                                {{ $member->name }} ({{ $member->email }})
                                <span class="text-xs text-slate-500 dark:text-gray-400 italic ml-2">
                                    {{ $team->owner_id === $member->id ? 'Propriétaire' : ($member->pivot->role === 'admin' ? 'Admin' : 'Membre') }}
                                </span>
                            </span>
                                @if ($isTeamAdmin)
                                    <div class="flex items-center space-x-2">
                                        @if ($member->pivot->role !== 'admin' && $team->owner_id !== $member->id)
                                            <button
                                                wire:click="promoteToAdmin({{ $member->id }})"
                                                class="bg-emerald-600 dark:bg-green-600 hover:bg-emerald-700 dark:hover:bg-green-700 text-white text-xs py-1 px-2 rounded transition-colors duration-200"
                                                title="Promouvoir en admin"
                                            >
                                                👑 Admin
                                            </button>
                                        @elseif ($member->pivot->role === 'admin' && $team->owner_id !== $member->id)
                                            <button
                                                wire:click="demoteFromAdmin({{ $member->id }})"
                                                class="bg-amber-600 dark:bg-orange-600 hover:bg-amber-700 dark:hover:bg-orange-700 text-white text-xs py-1 px-2 rounded transition-colors duration-200"
                                                title="Rétrograder en membre"
                                            >
                                                ⬇️ Membre
                                            </button>
                                        @endif

                                        @if ($team->owner_id !== $member->id)
                                            <button
                                                wire:click="removeMember({{ $member->id }})"
                                                class="bg-red-500 dark:bg-red-600 hover:bg-red-600 dark:hover:bg-red-700 text-white text-xs py-1 px-2 rounded transition-colors duration-200 font-medium"
                                                title="Supprimer de l'équipe"
                                            >
                                                🗑️ Supprimer
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                        @if ($isTeamAdmin)
                            <div class="flex items-center space-x-2">
                                <input
                                    type="email"
                                    wire:model="newMemberEmail"
                                    placeholder="Email du membre à ajouter"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white placeholder-slate-500 dark:placeholder-gray-400 focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                                >
                                <button
                                    wire:click="addMember"
                                    class="bg-emerald-600 dark:bg-blue-600 hover:bg-emerald-700 dark:hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
                                >
                                    Ajouter
                                </button>
                            </div>
                            @error('newMemberEmail')
                                <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bouton Nouveau projet -->
            <div class="flex justify-end mt-2 mb-6">
                                <button
                    wire:click="toggleCreateForm"
                    class="bg-slate-200 dark:bg-gray-800 hover:bg-slate-300 dark:hover:bg-gray-700 text-slate-800 dark:text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-lg cursor-pointer"
                    >
                    {{ $showCreateForm ? 'Annuler' : 'Nouveau projet' }}
                </button>
            </div>
        </div>

        <livewire:ui.flash-message />

        <!-- Create Project Form -->
        @if($showCreateForm)
            <div class="bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-500 rounded-lg p-6 mb-8 shadow-lg">
                <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-4">Créer un nouveau projet</h3>
                <form wire:submit.prevent="createProject">
                    <div class="mb-4">
                        <label for="newProjectName" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                            Nom du projet
                        </label>
                        <input
                            type="text"
                            id="newProjectName"
                            wire:model="newProjectName"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white placeholder-slate-500 dark:placeholder-gray-400 focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                            placeholder="Entrez le nom du projet"
                            required>
                        @error('newProjectName')
                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="newProjectDescription" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                            Description (optionnelle)
                        </label>
                        <textarea
                            id="newProjectDescription"
                            wire:model="newProjectDescription"
                            rows="3"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white placeholder-slate-500 dark:placeholder-gray-400 focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                            placeholder="Décrivez le projet..."></textarea>
                        @error('newProjectDescription')
                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="newProjectStartDate" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                                Date de début (optionnelle)
                            </label>
                            <input
                                type="date"
                                id="newProjectStartDate"
                                wire:model="newProjectStartDate"
                                class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                            @error('newProjectStartDate')
                                <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="newProjectEndDate" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                                Date de fin (optionnelle)
                            </label>
                            <input
                                type="date"
                                id="newProjectEndDate"
                                wire:model="newProjectEndDate"
                                class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                            @error('newProjectEndDate')
                                <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="newProjectStatus" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                            Statut du projet
                        </label>
                        <select
                            id="newProjectStatus"
                            wire:model="newProjectStatus"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500">
                            <option value="active">Actif</option>
                            <option value="archived">Archivé</option>
                        </select>
                        @error('newProjectStatus')
                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="submit"
                            class="bg-emerald-600 dark:bg-green-600 hover:bg-emerald-700 dark:hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                            Créer le projet
                        </button>
                        <button
                            type="button"
                            wire:click="toggleCreateForm"
                            class="bg-slate-400 dark:bg-gray-600 hover:bg-slate-500 dark:hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <div class="bg-slate-50 dark:bg-gray-800 border border-slate-200 dark:border-gray-600 rounded-lg p-6 hover:bg-slate-100 dark:hover:bg-gray-700 hover:border-slate-300 dark:hover:border-gray-500 transition-all duration-200 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-white cursor-pointer"
                            onclick="window.location.href='/projects/{{ $project->id }}/tasks'">
                            {{ $project->name }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            @can('deleteProject', $project)
                                <button
                                    wire:click="deleteProject({{ $project->id }})"
                                    onclick="event.stopPropagation(); return confirm('Êtes-vous sûr de vouloir supprimer ce projet ? Cette action est irréversible.')"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1"
                                    title="Supprimer le projet"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            @endcan
                            <svg class="w-3 h-3 text-slate-400 dark:text-gray-300 cursor-pointer"
                                 onclick="window.location.href='/projects/{{ $project->id }}/tasks'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div onclick="window.location.href='/projects/{{ $project->id }}/tasks'" class="cursor-pointer">
                        @if($project->description)
                            <p class="text-slate-600 dark:text-gray-300 mb-4 text-sm">{{ Str::limit($project->description, 100) }}</p>
                        @endif
                        <div class="text-sm text-slate-600 dark:text-gray-300">
                            @if($project->start_date || $project->end_date)
                                <p class="mb-2">
                                    <span class="font-medium">Période:</span>
                                    @if($project->start_date)
                                        {{ $project->start_date->format('d/m/Y') }}
                                    @else
                                        Non définie
                                    @endif
                                    @if($project->end_date)
                                        - {{ $project->end_date->format('d/m/Y') }}
                                    @endif
                                </p>
                            @endif
                            <p class="mb-2">
                                <span class="font-medium">Statut:</span>
                                <span class="px-2 py-1 rounded text-xs {{ $project->status === 'active' ? 'bg-emerald-600 dark:bg-green-600 text-white' : 'bg-slate-400 dark:bg-gray-600 text-white' }}">
                                    {{ $project->status === 'active' ? 'Actif' : 'Archivé' }}
                                </span>
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Tâches:</span> {{ $project->tasks()->count() }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Propriétaire:</span> {{ $project->owner->name }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-gray-400">
                                Créé le {{ $project->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-8 w-8 text-slate-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-slate-700 dark:text-gray-300 mb-2">Aucun projet</h3>
                    <p class="text-slate-600 dark:text-gray-500 mb-4">Cette équipe n'a pas encore de projets.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
