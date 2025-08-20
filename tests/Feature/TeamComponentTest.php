<?php

use App\Livewire\Dashboard;
use App\Models\User;
use App\Models\Team;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// --------------------------------------------------
// Helpers
// --------------------------------------------------

/**
 * Crée une team et attache $user avec un rôle.
 */
function attachMember(Team $team, User $user, string $role = 'user'): void
{
    $team->users()->attach($user->id, ['role' => $role]);
}

/**
 * Crée N équipes appartenant à $user (membre), avec un nom préfixé.
 * @return \Illuminate\Support\Collection<Team>
 */
function makeTeamsFor(User $user, int $count, string $prefix = 'Team'): \Illuminate\Support\Collection
{
    return collect(range(1, $count))->map(function ($i) use ($user, $prefix) {
        $team = Team::factory()->create();
        attachMember($team, $user);
        $team->update(['name' => "{$prefix} {$i}"]);
        return $team;
    });
}

// --------------------------------------------------
// Tests
// --------------------------------------------------

it('n’affiche que les équipes dont l’utilisateur est membre', function () {
    $me = User::factory()->create(['role' => 'user']);
    $otherUser = User::factory()->create(['role' => 'user']);

    $myTeam = Team::factory()->create(['name' => 'Ma Team']);
    attachMember($myTeam, $me);

    $foreignTeam = Team::factory()->create(['name' => 'Team étrangère']);
    attachMember($foreignTeam, $otherUser);

    $this->actingAs($me);

    Livewire::test(Dashboard::class)
        ->assertStatus(200)
        ->assertSee('Ma Team')
        ->assertDontSee('Team étrangère');
});

it('recherche par nom et description, sans sortir du scope utilisateur', function () {
    $me = User::factory()->create(['role' => 'user']);
    $other = User::factory()->create(['role' => 'user']);

    // Ma team avec description distinctive
    $mine = Team::factory()->create(['name' => 'Rocket Team', 'description' => 'fusée bleu électrique']);
    attachMember($mine, $me);

    // Une autre team avec description qui matcherait la recherche, mais je n’en suis pas membre
    $foreign = Team::factory()->create(['name' => 'Foreign', 'description' => 'fusée bleu électrique']);
    attachMember($foreign, $other);

    $this->actingAs($me);

    Livewire::test(Dashboard::class)
        ->set('search', 'fusée bleu')
        ->assertSee('Rocket Team')
        ->assertDontSee('Foreign');
});

it('réinitialise la pagination quand la recherche change', function () {
    $me = User::factory()->create();
    makeTeamsFor($me, 25, 'P'); // P 1..P 25
    $this->actingAs($me);

    Livewire::test(\App\Livewire\Dashboard::class)
        // Page 2
        ->call('gotoPage', 2)
        ->assertSee('P 19')     // en page 2
        ->assertDontSee('P 11') //  en page 1
        // Changer la recherche => updatingSearch() -> resetPage()
        ->set('search', 'P 1')
        ->assertSee('P 1');     // en page 1 filtrée
});

it('tri par défaut par nom asc et bascule asc/desc sur le même champ', function () {
    $me = User::factory()->create();

    // 3 équipes dans un ordre non trié
    $a = Team::factory()->create(['name' => 'Alpha']);
    $b = Team::factory()->create(['name' => 'Bravo']);
    $c = Team::factory()->create(['name' => 'Charlie']);
    attachMember($a, $me);
    attachMember($b, $me);
    attachMember($c, $me);

    $this->actingAs($me);

    Livewire::test(Dashboard::class)
        ->assertSeeInOrder(['Alpha', 'Bravo', 'Charlie'])

        // Clique sur le même champ -> bascule en desc
        ->call('sortBy', 'name')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortDirection', 'desc')
        ->assertSeeInOrder(['Charlie', 'Bravo', 'Alpha']);
});

it('changer de champ de tri le remet en asc et reset la pagination', function () {
    $me = User::factory()->create();
    makeTeamsFor($me, 12, 'T'); // T 1..T 12
    $this->actingAs($me);

    Livewire::test(\App\Livewire\Dashboard::class)
        // Page 2 (tri par défaut: name ASC -> T 8, T 9)
        ->call('gotoPage', 2)
        ->assertSee('T 8')
        ->assertSee('T 9')

        // Change de champ -> asc + resetPage()
        ->call('sortBy', 'created_at')
        ->assertSet('sortBy', 'created_at')
        ->assertSet('sortDirection', 'asc')

        // Après reset: created_at ASC => page 1 = T 1..T 10
        ->assertSee('T 1')
        ->assertSee('T 10')
        ->assertDontSee('T 11')   // T 11 n'est plus visible, il est en page 2
        ->assertDontSee('T 12');  // idem
});

it('pagine par 10 et inclut les compteurs users/projects', function () {
    $me = User::factory()->create();
    makeTeamsFor($me, 12, 'Paginate');
    $this->actingAs($me);

    Livewire::test(\App\Livewire\Dashboard::class)
        ->assertViewHas('teams', function ($paginator) {
            return $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
                && $paginator->count() === 10;
        })
        ->assertSee('Paginate 1')
        ->assertSee('Paginate 10')
        ->assertSee('Paginate 11')   // en page 1
        ->assertDontSee('Paginate 8'); // pas en page 1
});


it('respecte la recherche combinée au tri', function () {
    $me = User::factory()->create();

    $x = Team::factory()->create(['name' => 'Zeta']);
    $y = Team::factory()->create(['name' => 'Omega']);
    $z = Team::factory()->create(['name' => 'Alpha Omega']);
    attachMember($x, $me);
    attachMember($y, $me);
    attachMember($z, $me);

    $this->actingAs($me);

    Livewire::test(Dashboard::class)
        ->set('search', 'Omega')
        ->assertSee('Omega')
        ->assertSee('Alpha Omega')
        ->assertDontSee('Zeta')

        ->call('sortBy', 'name')
        ->assertSet('sortDirection', 'desc')
        ->assertSeeInOrder(['Omega', 'Alpha Omega']);
});
