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
 * Crée une team, un user courant (avec rôle site + rôle pivot équipe), et attache des membres "simples".
 *
 * @param string $teamRole   Rôle du user courant dans la team: 'admin' | 'user' | 'rh'
 * @param string $siteRole   Rôle site du user courant: 'admin' | 'user'
 * @param int    $others     Nombre d'autres membres à ajouter (en pivot 'user')
 * @return array [Team $team, User $current, array $othersArr]
 */
function bootTeam(string $teamRole = 'admin', string $siteRole = 'user', int $others = 0): array
{
    $team = Team::factory()->create();
    $current = User::factory()->create(['role' => $siteRole]); // rôle site

    // Attach courant avec rôle pivot équipe (nouvelle logique: 'admin' | 'user' | 'rh')
    $team->users()->attach($current->id, ['role' => $teamRole]);

    $othersArr = [];
    for ($i = 0; $i < $others; $i++) {
        $u = User::factory()->create(['role' => 'user']); // rôle site user par défaut
        $team->users()->attach($u->id, ['role' => 'user']); // pivot user par défaut
        $othersArr[] = $u;
    }

    return [$team, $current, $othersArr];
}

// --- Tests -----------------------------------------------------------------

it('affiche les projets de la team et calcule isAdmin', function () {
    // user courant = pivot admin dans la team
    [$team, $admin] = bootTeam(teamRole: 'admin', siteRole: 'user');

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
    // Crée une team avec un admin (peu importe), mais on va se connecter avec un "stranger"
    [$team] = bootTeam(teamRole: 'admin', siteRole: 'user');
    $stranger = User::factory()->create(['role' => 'user']); // rôle site user, non membre de la team

    $this->actingAs($stranger);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->assertForbidden();
});

it('permet de créer un projet (form + validation + état)', function () {
    [$team, $admin] = bootTeam(teamRole: 'admin', siteRole: 'user');
    $this->actingAs($admin);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('showCreateForm', true)
        ->set('newProjectName', 'Nouveau Projet')
        ->set('newProjectDescription', 'Description super')
        ->set('newProjectStartDate', now()->toDateString())
        ->set('newProjectEndDate', now()->addWeek()->toDateString())
        ->set('newProjectStatus', 'active')
        ->call('createProject')
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
    [$team, $admin] = bootTeam(teamRole: 'admin', siteRole: 'user');
    $this->actingAs($admin);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('showCreateForm', true)
        ->set('newProjectName', '')
        ->set('newProjectStatus', 'invalid')
        ->call('createProject')
        ->assertHasErrors(['newProjectName' => 'required', 'newProjectStatus' => 'in']);
});

it('calcule isAdmin=false pour un simple membre et cache les actions admin', function () {
    // user courant = pivot 'user' (ex "membre simple")
    [$team, $member, $others] = bootTeam(teamRole: 'user', siteRole: 'user', others: 1);
    $this->actingAs($member);

    $other = $others[0];

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->assertSet('isTeamAdmin', false)
        ->assertDontSee('Supprimer')
        ->assertDontSee('Ajouter');
});

it('ajoute un membre par email (admin)', function () {
    [$team, $admin] = bootTeam(teamRole: 'admin', siteRole: 'user', others: 1);
    $this->actingAs($admin);

    $newUser = User::factory()->create(['role' => 'user']);

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
    [$team, $admin, $others] = bootTeam(teamRole: 'admin', siteRole: 'user', others: 1);
    $this->actingAs($admin);

    $already = $others[0];

    expect($team->users->pluck('id'))->toContain($already->id);

    $initialCount = $team->users()->count();

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->set('newMemberEmail', $already->email)
        ->call('addMember')
        ->assertSet('newMemberEmail', '');

    $team->refresh();
    expect($team->users()->count())->toBe($initialCount);
});

it('valide l’email lors de l’ajout de membre', function () {
    [$team, $admin] = bootTeam(teamRole: 'admin', siteRole: 'user');
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
    [$team, $admin, $others] = bootTeam(teamRole: 'admin', siteRole: 'user', others: 1);
    $this->actingAs($admin);
    $member = $others[0];

    expect($team->users->pluck('id'))->toContain($member->id);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->call('removeMember', $member->id);

    $team->refresh();
    expect($team->users->pluck('id'))->not()->toContain($member->id);
});

it('liste uniquement les projets de la team dans la grille', function () {
    [$team, $admin] = bootTeam(teamRole: 'admin', siteRole: 'user');
    $this->actingAs($admin);

    $mine = Project::factory()->create(['team_id' => $team->id, 'owner_id' => $admin->id, 'name' => 'Projet A']);
    $otherTeamProject = Project::factory()->create(['name' => 'Projet B']);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->assertSee('Projet A')
        ->assertDontSee('Projet B');
});

it('autorise un site admin même hors équipe', function () {
    [$team] = bootTeam(teamRole:'admin', siteRole:'user');
    $super = User::factory()->create(['role' => 'admin']); // site-admin
    $this->actingAs($super);

    Livewire::test(ProjectComponent::class, ['teamId' => $team->id])
        ->assertStatus(200);
});

