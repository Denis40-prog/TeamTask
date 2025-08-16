<?php

use App\Livewire\ProjectComponent;
use App\Models\User;
use App\Models\Team;
use App\Models\Project;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

// --- Helpers ---------------------------------------------------------------

/**
 * Crée une team, un user courant (admin ou membre), et attache les membres fournis.
 * @return array [team, currentUser, others(array)]
 */
function bootTeam(bool $asAdmin = true, int $others = 0): array
{
    $team = Team::factory()->create();
    $current = User::factory()->create();

    // Pivot role
    $team->users()->attach($current->id, ['role' => $asAdmin ? 'admin' : 'member']);

    $othersArr = [];
    for ($i = 0; $i < $others; $i++) {
        $u = User::factory()->create();
        $team->users()->attach($u->id, ['role' => 'member']);
        $othersArr[] = $u;
    }

    return [$team, $current, $othersArr];
}

// --- Tests -----------------------------------------------------------------

it('affiche les projets de la team et calcule isAdmin', function () {
    [$team, $admin] = bootTeam(asAdmin: true);

    // 2 projets visibles pour la team
    Project::factory()->count(2)->create([
        'team_id'  => $team->id,
        'owner_id' => $admin->id,
        'status'   => 'active',
    ]);

    // 1 projet dans une autre team -> non visible
    Project::factory()->create();

    $this->actingAs($admin);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->assertStatus(200)
        ->assertSet('isTeamAdmin', true)
        ->assertSee($team->name)
        ->assertSee(Project::where('team_id', $team->id)->first()->name)
        ->assertDontSee(Project::where('team_id', '!=', $team->id)->first()->name ?? '');
});

it('refuse l’accès si le user ne fait pas partie de la team (403)', function () {
    [$team] = bootTeam(asAdmin: true);
    $stranger = User::factory()->create();
    $this->actingAs($stranger);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->assertForbidden();
});

it('permet de créer un projet (form + validation + état)', function () {
    [$team, $admin] = bootTeam(asAdmin: true);
    $this->actingAs($admin);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('showCreateForm', true)
        ->set('newProjectName', 'Nouveau Projet')
        ->set('newProjectDescription', 'Description super')
        ->set('newProjectStartDate', now()->toDateString())
        ->set('newProjectEndDate', now()->addWeek()->toDateString())
        ->set('newProjectStatus', 'active')
        ->call('createProject')
        // état réinitialisé
        ->assertSet('showCreateForm', false)
        ->assertSet('newProjectName', '')
        ->assertSet('newProjectDescription', '')
        ->assertSet('newProjectStartDate', '')
        ->assertSet('newProjectEndDate', '')
        ->assertSet('newProjectStatus', 'active');

    $this->assertDatabaseHas('projects', [
        'team_id' => $team->id,
        'owner_id' => $admin->id,
        'name' => 'Nouveau Projet',
        'status' => 'active',
    ]);
});

it('valide la création de projet (nom requis, statut dans liste)', function () {
    [$team, $admin] = bootTeam(asAdmin: true);
    $this->actingAs($admin);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('showCreateForm', true)
        ->set('newProjectName', '')
        ->set('newProjectStatus', 'invalid')
        ->call('createProject')
        ->assertHasErrors(['newProjectName' => 'required', 'newProjectStatus' => 'in']);
});

it('calcule isAdmin=false pour un simple membre et cache les actions admin', function () {
    [$team, $member, $others] = bootTeam(asAdmin: false, others: 1);
    $this->actingAs($member);

    $other = $others[0];

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->assertSet('isTeamAdmin', false)
        // On ne voit pas le bouton "Supprimer" des membres
        ->assertDontSee('Supprimer')
        // On ne voit pas le bouton "Ajouter" (section admin)
        ->assertDontSee('Ajouter');
});

it('ajoute un membre par email (admin)', function () {
    [$team, $admin] = bootTeam(asAdmin: true, others: 1);
    $this->actingAs($admin);

    $newUser = User::factory()->create();

    $initialCount = $team->users()->count();

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('newMemberEmail', $newUser->email)
        ->call('addMember')
        ->assertSet('newMemberEmail', '');

    $team->refresh();
    expect($team->users()->count())->toBe($initialCount + 1);
    expect($team->users->pluck('id'))->toContain($newUser->id);
});

it('n’ajoute pas deux fois le même membre et remonte un message', function () {
    [$team, $admin, $others] = bootTeam(asAdmin: true, others: 1);
    $this->actingAs($admin);

    $already = $others[0];

    // Vérifie que l’utilisateur est déjà dans l’équipe
    expect($team->users->pluck('id'))->toContain($already->id);

    $initialCount = $team->users()->count();

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('newMemberEmail', $already->email)
        ->call('addMember')
        // pas de plantage, mais pas d’ajout non plus
        ->assertSet('newMemberEmail', '');

    $team->refresh();
    expect($team->users()->count())->toBe($initialCount);
});

it('valide l’email lors de l’ajout de membre', function () {
    [$team, $admin] = bootTeam(asAdmin: true);
    $this->actingAs($admin);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('newMemberEmail', 'not-an-email')
        ->call('addMember')
        ->assertHasErrors(['newMemberEmail' => 'email']);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('newMemberEmail', 'ghost@example.com')
        ->call('addMember')
        ->assertHasErrors(['newMemberEmail' => 'exists']);
});

it('retire un membre (admin)', function () {
    [$team, $admin, $others] = bootTeam(asAdmin: true, others: 1);
    $this->actingAs($admin);
    $member = $others[0];

    expect($team->users->pluck('id'))->toContain($member->id);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->call('removeMember', $member->id);

    $team->refresh();
    expect($team->users->pluck('id'))->not()->toContain($member->id);
});

it('liste uniquement les projets de la team dans la grille', function () {
    [$team, $admin] = bootTeam(asAdmin: true);
    $this->actingAs($admin);

    $mine = Project::factory()->create(['team_id' => $team->id, 'owner_id' => $admin->id, 'name' => 'Projet A']);
    $otherTeamProject = Project::factory()->create(['name' => 'Projet B']);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->assertSee('Projet A')
        ->assertDontSee('Projet B');
});


