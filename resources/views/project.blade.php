@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Titre du projet -->
    <h1 class="text-3xl font-bold text-white mb-6">{{ $project->name }}</h1>

    <!-- Team associée -->
    <h2 class="text-xl font-semibold text-gray-500 mb-4">Équipe associée : {{ $project->team->name }}</h2>

    @livewire('tasks', ['project' => $project])

    @livewire('comments', ['project' => $project])

</div>
@endsection
