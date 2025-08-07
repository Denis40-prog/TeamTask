<div>
    <div class="container mx-auto px-4 py-8 text-white">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-400 mb-2">
                    <a href="/dashboard" class="hover:text-white transition-colors">Dashboard</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-white">{{ $team->name }}</span>
                </nav>
                <h1 class="text-3xl font-bold text-white">Projets de {{ $team->name }}</h1>
            </div>
            <button
                wire:click="toggleCreateForm"
                class="bg-gray-800 hover:bg-blue-900 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-lg cursor-pointer">
                {{ $showCreateForm ? 'Annuler' : 'Nouveau projet' }}
            </button>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div
                x-data="{ show: true }"
                x-show="show"
                class="bg-green-600 text-white p-4 rounded-lg mb-6 relative flex items-center justify-between"
            >
                <div class="pr-8">
                    {{ session('message') }}
                </div>
                <button
                    @click="show = false"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white text-lg leading-none hover:text-gray-300"
                >
                    &times;
                </button>
            </div>
        @endif

        <!-- Create Project Form -->
        @if($showCreateForm)
            <div class="bg-gray-700 border-2 border-gray-500 rounded-lg p-6 mb-8 shadow-lg">
                <h3 class="text-xl font-semibold text-white mb-4">Créer un nouveau projet</h3>
                <form wire:submit.prevent="createProject">
                    <div class="mb-4">
                        <label for="newProjectName" class="block text-sm font-medium text-gray-300 mb-2">
                            Nom du projet
                        </label>
                        <input
                            type="text"
                            id="newProjectName"
                            wire:model="newProjectName"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                            placeholder="Entrez le nom du projet"
                            required>
                        @error('newProjectName')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="newProjectDescription" class="block text-sm font-medium text-gray-300 mb-2">
                            Description (optionnelle)
                        </label>
                        <textarea
                            id="newProjectDescription"
                            wire:model="newProjectDescription"
                            rows="3"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                            placeholder="Décrivez le projet..."></textarea>
                        @error('newProjectDescription')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="newProjectStartDate" class="block text-sm font-medium text-gray-300 mb-2">
                                Date de début (optionnelle)
                            </label>
                            <input
                                type="date"
                                id="newProjectStartDate"
                                wire:model="newProjectStartDate"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                            @error('newProjectStartDate')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="newProjectEndDate" class="block text-sm font-medium text-gray-300 mb-2">
                                Date de fin (optionnelle)
                            </label>
                            <input
                                type="date"
                                id="newProjectEndDate"
                                wire:model="newProjectEndDate"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                            @error('newProjectEndDate')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="newProjectStatus" class="block text-sm font-medium text-gray-300 mb-2">
                            Statut du projet
                        </label>
                        <select
                            id="newProjectStatus"
                            wire:model="newProjectStatus"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500">
                            <option value="active">Actif</option>
                            <option value="archived">Archivé</option>
                        </select>
                        @error('newProjectStatus')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                            Créer le projet
                        </button>
                        <button
                            type="button"
                            wire:click="toggleCreateForm"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <div class="bg-gray-800 border-2 border-gray-600 rounded-lg p-6 hover:bg-gray-750 hover:border-neutral-50 transition-all duration-200 cursor-pointer shadow-lg"
                     onclick="window.location.href='/projects/{{ $project->id }}/tasks'">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-white">{{ $project->name }}</h3>
                        <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    @if($project->description)
                        <p class="text-gray-300 mb-4 text-sm">{{ Str::limit($project->description, 100) }}</p>
                    @endif
                    <div class="text-sm text-gray-300">
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
                            <span class="px-2 py-1 rounded text-xs {{ $project->status === 'active' ? 'bg-green-600 text-green-100' : 'bg-gray-600 text-gray-300' }}">
                                {{ $project->status === 'active' ? 'Actif' : 'Archivé' }}
                            </span>
                        </p>
                        <p class="mb-2">
                            <span class="font-medium">Tâches:</span> {{ $project->tasks()->count() }}
                        </p>
                        <p class="mb-2">
                            <span class="font-medium">Propriétaire:</span> {{ $project->owner->name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            Créé le {{ $project->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-8 w-8 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-300 mb-2">Aucun projet</h3>
                    <p class="text-gray-500 mb-4">Cette équipe n'a pas encore de projets.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
