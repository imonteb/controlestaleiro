<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders the ferramentas create form for logistics users', function () {
    $user = User::factory()->create([
        'role' => [User::ROLE_LOGI],
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('ferramentas.crear'));

    $response->assertOk();
    $response->assertSee('Nova Ferramenta');
    $response->assertSee('Dados do Equipamento');
    $response->assertSee('Designação do Equipamento');
    $response->assertSee('Controlo Preventivo');
    $response->assertSee('Criar Equipamento');
});

it('redirects guests away from ferramentas form', function () {
    $response = $this->get(route('ferramentas.crear'));

    $response->assertRedirect(route('login'));
});

it('forbids non-logi users from accessing ferramentas form', function () {
    $user = User::factory()->create([
        'role' => [User::ROLE_OPERARIO],
    ]);

    $this->actingAs($user)
        ->get(route('ferramentas.crear'))
        ->assertForbidden();
});
