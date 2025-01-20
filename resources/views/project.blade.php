@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Titre du projet -->
    <h1 class="text-3xl font-bold text-gray-700 mb-6">{{ $project->name }}</h1>

    <!-- Team associée -->
    <h2 class="text-xl font-semibold text-gray-500 mb-4">Équipe associée : {{ $project->team->name }}</h2>

    <!-- Tableau des tasks -->
    <div class="rounded shadow-md p-6 mb-6 bg-gray-800 text-gray-400">
        <h2 class="text-2xl font-semibold mb-4">Tasks</h2>

        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2 text-left">Statut</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Nom</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Attribué à</th>
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
                            <td class="border border-gray-300 px-4 py-2">{{ $task->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $task->assignedTo ? $task->assignedTo->name : 'Non attribué' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <!-- Exemple de boutons d'actions -->
                                <a href="{{ route('task.edit', $task->id) }}" class="text-blue-500">Modifier</a>
                                <form action="{{ route('task.destroy', $task->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 ml-2">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Formulaire de création de task -->
    <div class="rounded shadow-md p-6 mb-6 bg-gray-800 text-gray-400">
        <h2 class="text-2xl font-semibold mb-4">Créer une nouvelle tâche</h2>
        <form action="{{ route('task.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="name" class="block text-white">Nom de la tâche :</label>
                    <input type="text" name="name" id="name" class="w-full border-gray-300 rounded p-2" required>
                </div>
                <div>
                    <label for="assigned_to" class="block text-white">Attribuer à :</label>
                    <select name="assigned_to" id="assigned_to" class="w-full border-gray-300 rounded p-2">
                        <option value="">Non attribué</option>
                        @foreach ($project->team->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-white">Statut :</label>
                    <select name="status" id="status" class="w-full border-gray-300 rounded p-2" required>
                        <option value="À faire">À faire</option>
                        <option value="En cours">En cours</option>
                        <option value="À tester">À tester</option>
                        <option value="Validé">Validé</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Créer</button>
        </form>
    </div>

    <!-- Commentaires -->
    <div class="bg-gray-800 text-gray-400 rounded shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">Commentaires</h2>

        <!-- Liste des commentaires -->
        <ul class="mb-4">
            @forelse ($comments as $comment)
                <li class="border-b border-gray-200 pb-2 mb-2">
                    <strong class="text-gray-700">{{ $comment->user->name }}</strong> :
                    <span class="text-gray-600">{{ $comment->content }}</span>
                </li>
            @empty
                <li class="text-gray-500">Aucun commentaire pour ce projet.</li>
            @endforelse
        </ul>

        <!-- Formulaire pour ajouter un commentaire -->
        <form action="{{ route('comment.store') }}" method="POST">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div>
                <label for="content" class="block text-gray-700">Votre commentaire :</label>
                <textarea name="content" id="content" class="w-full border-gray-300 rounded p-2" rows="3" required></textarea>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Ajouter</button>
        </form>
    </div>
</div>
@endsection
