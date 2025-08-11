<div>
    <div class="container mx-auto px-4 py-8 text-white">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Dashboard</h1>
                <p class="text-gray-400 mt-2">Bienvenue, {{ Auth::user()->name }}</p>
            </div>
            <button
                wire:click="toggleCreateForm"
                class="bg-gray-800 hover:bg-blue-900 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-lg cursor-pointer">
                {{ $showCreateForm ? 'Annuler' : 'Nouvelle équipe' }}
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

        <!-- Create Team Form -->
        @if($showCreateForm)
            <div class="bg-gray-800 rounded-lg p-6 mb-8">
                <h3 class="text-xl font-semibold text-white mb-4">Créer une nouvelle équipe</h3>
                <form wire:submit.prevent="createTeam">
                    <div class="mb-4">
                        <label for="newTeamName" class="block text-sm font-medium text-gray-300 mb-2">
                            Nom de l'équipe
                        </label>
                        <input
                            type="text"
                            id="newTeamName"
                            wire:model="newTeamName"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                            placeholder="Entrez le nom de l'équipe"
                            required>
                        @error('newTeamName')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="newTeamDescription" class="block text-sm font-medium text-gray-300 mb-2">
                            Description de l'équipe (optionnel)
                        </label>
                        <textarea
                            id="newTeamDescription"
                            wire:model="newTeamDescription"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                            placeholder="Décrivez l'équipe et ses objectifs"
                            rows="3"></textarea>
                        @error('newTeamDescription')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                            Créer l'équipe
                        </button>
                        <button
                            type="button"
                            wire:click="toggleCreateForm"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Teams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($teams as $team)
                <div class="bg-gray-800 border-2 border-gray-600 rounded-lg p-6 hover:bg-gray-750 hover:border-neutral-50 transition-all duration-200 cursor-pointer shadow-lg"
                     onclick="window.location.href='/projects/{{ $team->id }}'">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-white">{{ $team->name }}</h3>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    @if($team->description)
                        <p class="text-gray-300 text-sm mb-3">{{ Str::limit($team->description, 100) }}</p>
                    @endif
                    <div class="text-sm text-gray-400">
                        <p class="mb-2">
                            <span class="font-medium">Membres:</span> {{ $team->users()->count() }}
                        </p>
                        <p class="mb-2">
                            <span class="font-medium">Projets:</span> {{ $team->projects()->count() }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Créée le {{ $team->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-8 w-8 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-300 mb-2">Aucune équipe</h3>
                    <p class="text-gray-500 mb-4">Vous ne faites partie d'aucune équipe pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
