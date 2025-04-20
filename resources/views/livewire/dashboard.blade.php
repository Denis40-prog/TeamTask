<div>
    <div class="container mx-auto p-4 flex justify-center items-start min-h-screen">
        <div class="w-full max-w-4xl">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-400">Bienvenue, {{ auth()->user()->name }}</h1>

            <!-- Ligne contenant les deux sections -->
            <div class="flex w-full justify-between space-x-8">
                <!-- Colonne "Vos Équipes" -->
                <div class="flex-1 bg-gray-800 p-4 rounded shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-gray-400">Vos Équipes</h2>
                    @if($teams->isEmpty())
                        <p class="text-gray-500">Vous n'êtes membre d'aucune équipe.</p>
                    @else
                        <ul class="list-disc pl-5 text-gray-500">
                            @foreach($teams as $team)
                                <li class="mb-1"><a href="team/{{$team->id}}">{{ $team->name }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Colonne "Vos Projets" -->
                <div class="flex-1 bg-gray-800 p-4 rounded shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-gray-400">Vos Projets</h2>
                    @if($projects->isEmpty())
                        <p class="text-gray-500">Vous n'avez pas encore de projets.</p>
                    @else
                        <ul class="list-disc pl-5 text-gray-500">
                            @foreach($projects as $project)
                                <li class="mb-1"><a href="project/{{$project->id}}">{{ $project->name }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
