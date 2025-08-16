<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Gestion des utilisateurs</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2">
                    Gérez tous les utilisateurs inscrits sur la plateforme
                </p>
            </div>

            <!-- Filtres -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-4">Filtres</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Recherche par nom -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Nom d'utilisateur
                            </label>
                            <input wire:model.live="search" type="text" id="search"
                                   class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Rechercher par nom...">
                        </div>

                        <!-- Filtre par email -->
                        <div>
                            <label for="emailFilter" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Email
                            </label>
                            <input wire:model.live="emailFilter" type="email" id="emailFilter"
                                   class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Rechercher par email...">
                        </div>

                        <!-- Filtre par équipe -->
                        <div>
                            <label for="teamFilter" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Équipe
                            </label>
                            <select wire:model.live="teamFilter" id="teamFilter"
                                    class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Toutes les équipes</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtre par rôle -->
                        <div>
                            <label for="roleFilter" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Rôle
                            </label>
                            <select wire:model.live="roleFilter" id="roleFilter"
                                    class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tous les rôles</option>
                                <option value="admin">Administrateur</option>
                                <option value="user">Utilisateur</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button wire:click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700">
                            Effacer les filtres
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tableau des utilisateurs -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-900">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <button wire:click="sortBy('name')"
                                            class="group inline-flex items-center text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                                        Nom
                                        @if($sortBy === 'name')
                                            @if($sortDirection === 'asc')
                                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <button wire:click="sortBy('email')"
                                            class="group inline-flex items-center text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                                        Email
                                        @if($sortBy === 'email')
                                            @if($sortDirection === 'asc')
                                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    Rôle
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    Équipes
                                </th>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <button wire:click="sortBy('created_at')"
                                            class="group inline-flex items-center text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                                        Inscription
                                        @if($sortBy === 'created_at')
                                            @if($sortDirection === 'asc')
                                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">{{ $user->initials() }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                    {{ $user->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->role === 'admin')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                Administrateur
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Utilisateur
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                        <div class="max-w-xs">
                                            @if($user->teams->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($user->teams->take(3) as $team)
                                                        <span class="inline-flex px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                            {{ $team->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($user->teams->count() > 3)
                                                        <span class="inline-flex px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                                                            +{{ $user->teams->count() - 3 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 italic">Aucune équipe</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($user->role === 'user')
                                            <button wire:click="confirmPromoteUser({{ $user->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Promouvoir admin
                                            </button>
                                        @elseif($user->id !== auth()->id())
                                            <button wire:click="demoteFromAdmin({{ $user->id }})"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir rétrograder cet administrateur ?')">
                                                Rétrograder
                                            </button>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 italic">Vous</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-500 dark:text-slate-400">
                                        Aucun utilisateur trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white dark:bg-slate-800 px-4 py-3 border-t border-slate-200 dark:border-slate-700 sm:px-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation pour promotion -->
    @if($showPromoteModal && $userToPromote)
        <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-white">
                                        Promouvoir en administrateur
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            Êtes-vous sûr de vouloir promouvoir <strong>{{ $userToPromote->name }}</strong> en tant qu'administrateur du site ?
                                            Cette action lui donnera accès à toutes les fonctionnalités d'administration.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button wire:click="promoteToAdmin" type="button"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Promouvoir
                            </button>
                            <button wire:click="$set('showPromoteModal', false)" type="button"
                                    class="mt-3 inline-flex w-full justify-center rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-base font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
