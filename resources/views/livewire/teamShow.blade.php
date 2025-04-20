{{-- @extends('layouts.app')

@section('content') --}}

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-gray-400">{{ $team->name }}</h1>

    @if($teamLeader)
        <p class="text-gray-500 mb-4">Chef de l'équipe : <strong>{{ $teamLeader->name }}</strong></p>
    @endif

    <h2 class="text-xl font-semibold mb-2 text-gray-400">Membres de l'équipe</h2>
    @if($team->users->isEmpty())
        <p class="text-gray-500">Aucun membre dans cette équipe.</p>
    @else
        <ul class="list-disc pl-5 text-gray-500">
            @foreach($team->users as $user)
                <li class="mb-1 flex justify-between items-center">
                    {{ $user->name }}
                    <button wire:click="removeMember({{ $user->id }})" class="text-red-500 hover:underline">
                        Supprimer
                    </button>
                </li>
            @endforeach
        </ul>
    @endif

    <!-- Ajouter un membre -->
    <div class="mt-6 flex items-center">
        <label for="user_id" class="block text-gray-400 mb-2 mr-2">Ajouter un nouveau membre :</label>
        <select wire:model="newMemberId" id="user_id" class="w-full mt-2 p-2 border rounded mr-2">
            <option value="">-- Choisir un utilisateur --</option>
            @foreach($availableUsers as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <button wire:click="addMember" type="submit" class="mt-2 bg-green-500 text-white px-4 py-2 rounded">
            Ajouter
        </button>
    </div>

    @if(session()->has('message'))
        <div class="mt-4 bg-green-200 text-green-800 p-2 rounded">
            {{ session('message') }}
        </div>
    @endif
</div>

{{-- @endsection --}}
