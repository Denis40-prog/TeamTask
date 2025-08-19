<div>
    <div class="container mx-auto px-4 py-8 text-slate-800 dark:text-white dark:bg-transparent">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Dashboard</h1>
                <p class="text-slate-600 dark:text-gray-400 mt-2">Bienvenue, {{ Auth::user()->name }}</p>
            </div>
            <button
                wire:click="toggleCreateForm"
                class="bg-slate-200 dark:bg-zinc-900 hover:bg-slate-300 dark:hover:bg-gray-700 text-slate-800 dark:text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-lg cursor-pointer">
                {{ $showCreateForm ? 'Annuler' : 'Nouvelle équipe' }}
            </button>
        </div>

        <livewire:ui.flash-message />

        <!-- Create Team Form -->
        @if($showCreateForm)
            <div class="bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-gray-600 rounded-lg p-6 mb-8">
                <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-4">Créer une nouvelle équipe</h3>
                <form wire:submit.prevent="createTeam">
                    <div class="mb-4">
                        <label for="newTeamName" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                            Nom de l'équipe
                        </label>
                        <input
                            type="text"
                            id="newTeamName"
                            wire:model="newTeamName"
                            class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white placeholder-slate-500 dark:placeholder-gray-400 focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                            placeholder="Entrez le nom de l'équipe"
                            required>
                        @error('newTeamName')
                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="newTeamDescription" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">
                            Description de l'équipe (optionnel)
                        </label>
                        <textarea
                            id="newTeamDescription"
                            wire:model="newTeamDescription"
                            class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white placeholder-slate-500 dark:placeholder-gray-400 focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500"
                            placeholder="Décrivez l'équipe et ses objectifs"
                            rows="3"></textarea>
                        @error('newTeamDescription')
                            <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="submit"
                            class="bg-emerald-600 dark:bg-green-600 hover:bg-emerald-700 dark:hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                            Créer l'équipe
                        </button>
                        <button
                            type="button"
                            wire:click="toggleCreateForm"
                            class="bg-slate-400 dark:bg-gray-600 hover:bg-slate-500 dark:hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Teams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($teams as $team)
                <div class="bg-slate-100 dark:bg-zinc-900 border border-slate-200 dark:border-gray-600 rounded-lg p-6 hover:bg-slate-100 dark:hover:bg-gray-700 hover:border-slate-300 dark:hover:border-gray-500 transition-all duration-200 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-white cursor-pointer"
                            onclick="window.location.href='/projects/{{ $team->id }}'">
                            {{ $team->name }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            @can('deleteTeam', $team)
                                <button
                                    wire:click="deleteTeam({{ $team->id }})"
                                    onclick="event.stopPropagation(); return confirm('Êtes-vous sûr de vouloir supprimer cette équipe ? Cette action est irréversible et supprimera tous les projets, tâches et commentaires associés.')"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1"
                                    title="Supprimer l'équipe"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            @endcan
                            <svg class="w-5 h-5 text-slate-400 dark:text-gray-400 cursor-pointer"
                                 onclick="window.location.href='/projects/{{ $team->id }}'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div onclick="window.location.href='/projects/{{ $team->id }}'" class="cursor-pointer">
                        @if($team->description)
                            <p class="text-slate-600 dark:text-gray-300 text-sm mb-3">{{ Str::limit($team->description, 100) }}</p>
                        @endif
                        <div class="text-sm text-slate-600 dark:text-gray-400">
                            <p class="mb-2">
                                <span class="font-medium">Membres:</span> {{ $team->users()->count() }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Projets:</span> {{ $team->projects()->count() }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-gray-500">
                                Créée le {{ $team->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-8 w-8 text-slate-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-slate-700 dark:text-gray-300 mb-2">Aucune équipe</h3>
                    <p class="text-slate-600 dark:text-gray-500 mb-4">Vous ne faites partie d'aucune équipe pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
