<div>
    <div class="rounded shadow-md p-6 mb-6 bg-gray-800 text-gray-400">
        <h2 class="text-2xl font-semibold mb-4">Tasks</h2>

        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2 text-left">Statut</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Titre</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Description</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Priorité</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Attribué à</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Date limite</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach (['À faire', 'En cours', 'À tester', 'Validé'] as $status)
                                @php
                                    $tasksByStatus = $project->tasks->where('status', $status);
                                @endphp
                                @foreach ($tasksByStatus as $task)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">{{ $status }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $task->title }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $task->description }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $task->priority }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ $task->assignee_id ? $project->team->users->find($task->assignee_id)->name : 'Non attribué' }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $task->due_date }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <!-- Exemple de boutons d'actions -->
                                            <a href="{{ route('task.edit', $task->id) }}" class="text-blue-500">Modifier</a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 ml-2">Supprimer</button>
                                        </td>
                                    </tr>
                                @endforeach
                @endforeach
                {{ $tasks->links() }}
            </tbody>
        </table>
    </div>

    <!-- Formulaire de création de task -->
    <div class="rounded shadow-md p-6 mb-6 bg-gray-800 text-gray-400" wire:key="create-task-form">
        <h2 class="text-2xl font-semibold mb-4">Créer une nouvelle tâche</h2>
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                @error('title') <span class="error">{{ $message }}</span> @enderror
                <label for="title" class="block text-white">Nom de la tâche : *</label>
                <input type="text" wire:model="title" name="title" id="title" class="w-full border-gray-300 rounded p-2"
                    required>
            </div>
            <div>
                @error('description') <span class="error">{{ $message }}</span> @enderror
                <label for="description" class="block text-white">Description de la tâche : *</label>
                <input type="text" name="description" wire:model="description" id="description"
                    class="w-full border-gray-300 rounded p-2" required>
            </div>
            <div>
                <label for="status" class="block text-white">Statut :</label>
                <select name="status" wire:model="status" id="status" class="w-full border-gray-300 rounded p-2"
                    required>
                    <option value="À faire" selected>À faire</option>
                    <option value="En cours">En cours</option>
                    <option value="À tester">À tester</option>
                    <option value="Validé">Validé</option>
                </select>
            </div>
            <div>
                <label for="priority" class="block text-white">Priorité :</label>
                <select name="priority" wire:model="priority" id="priority" class="w-full border-gray-300 rounded p-2"
                    required>
                    <option value="Basse">Basse</option>
                    <option value="Moyenne" selected>Moyenne</option>
                    <option value="Élevée">Élevée</option>
                </select>
            </div>
            <div>
                <label for="due_date" class="block text-white">Date limite :</label>
                <input type="date" name="due_date" wire:model="dueDate" id="due_date"
                    class="w-full border-gray-300 rounded p-2">
            </div>
            <div>
                @error('assigneeId') <span class="error">{{ $message }}</span> @enderror
                <label for="assignee_id" class="block text-white">Attribuer à : *</label>
                <select name="assignee_id" wire:model="assigneeId" id="assignee_id"
                    class="w-full border-gray-300 rounded p-2" required>
                    <option value="">Non attribué</option>
                    @foreach ($project->team->users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button wire:click="createTask" type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Créer</button>
    </div>
</div>
