<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Équipes</h1>
        <button wire:click="showCreateForm" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Nouvelle équipe
        </button>
    </div>

    <!-- Search Bar -->
    <div class="mb-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Rechercher par nom ou description..."
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        >
    </div>

    <!-- Teams Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($teams as $team)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">#{{ $team->id }}</span>
                </div>

                @if($team->description)
                    <p class="text-gray-600 dark:text-gray-300 mb-4">{{ Str::limit($team->description, 100) }}</p>
                @endif

                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <span>👥 {{ $team->users_count }} membres</span>
                    <span>📁 {{ $team->projects_count }} projets</span>
                </div>

                <div class="border-t dark:border-gray-700 pt-4 flex justify-between items-center">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        Créée le {{ $team->created_at->format('d/m/Y') }}
                    </span>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Voir</button>
                        <button class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 text-sm">Modifier</button>
                        <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">Supprimer</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">Aucune équipe trouvée.</p>
            </div>
        @endforelse
    </div>    <!-- Pagination -->
    <div class="mt-6">
        {{ $teams->links() }}
    </div>
</div>
