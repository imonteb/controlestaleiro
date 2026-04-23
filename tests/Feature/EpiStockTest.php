<?php

use App\Models\Colaborador;
use App\Models\EpiEntrega;
use App\Models\EpiItem;
use App\Models\EpiRececao;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Observers are registered in AppServiceProvider — no manual boot needed.
    $this->user = User::factory()->create(['role' => ['epi']]);
    $this->colaborador = Colaborador::factory()->create();
});

// ─────────────────────────────────────────────
// RECEPCIONES — incrementan stock
// ─────────────────────────────────────────────

it('increases stock when a reception is created', function () {
    $item = EpiItem::factory()->withStock(5)->create();

    EpiRececao::create([
        'epi_item_id' => $item->id,
        'cantidad' => 3,
        'fecha' => today(),
        'registrado_por' => $this->user->id,
    ]);

    expect($item->fresh()->stock_total)->toBe(8);
});

it('adjusts stock when reception cantidad is updated', function () {
    $item = EpiItem::factory()->withStock(10)->create();

    $recepcion = EpiRececao::create([
        'epi_item_id' => $item->id,
        'cantidad' => 4,
        'fecha' => today(),
        'registrado_por' => $this->user->id,
    ]);

    expect($item->fresh()->stock_total)->toBe(14);

    $recepcion->update(['cantidad' => 6]);

    expect($item->fresh()->stock_total)->toBe(16); // +2 extra
});

it('decreases stock when a reception is deleted', function () {
    $item = EpiItem::factory()->withStock(10)->create();

    $recepcion = EpiRececao::create([
        'epi_item_id' => $item->id,
        'cantidad' => 5,
        'fecha' => today(),
        'registrado_por' => $this->user->id,
    ]);

    $recepcion->delete();

    expect($item->fresh()->stock_total)->toBe(10);
});

// ─────────────────────────────────────────────
// ENTREGAS — decrementan stock
// ─────────────────────────────────────────────

it('decreases stock when a delivery is created', function () {
    $item = EpiItem::factory()->withStock(10)->create();

    EpiEntrega::create([
        'epi_item_id' => $item->id,
        'colaborador_id' => $this->colaborador->id,
        'cantidad' => 3,
        'fecha_entrega' => today(),
        'estado' => 'entregue',
        'entregado_por' => $this->user->id,
    ]);

    expect($item->fresh()->stock_total)->toBe(7);
});

it('restores stock when delivery estado changes to devolvido', function () {
    $item = EpiItem::factory()->withStock(10)->create();

    $entrega = EpiEntrega::create([
        'epi_item_id' => $item->id,
        'colaborador_id' => $this->colaborador->id,
        'cantidad' => 4,
        'fecha_entrega' => today(),
        'estado' => 'entregue',
        'entregado_por' => $this->user->id,
    ]);

    expect($item->fresh()->stock_total)->toBe(6);

    $entrega->update(['estado' => 'devolvido']);

    expect($item->fresh()->stock_total)->toBe(10);
});

it('does not allow stock to go negative via observer', function () {
    $item = EpiItem::factory()->withStock(2)->create();

    EpiEntrega::create([
        'epi_item_id' => $item->id,
        'colaborador_id' => $this->colaborador->id,
        'cantidad' => 2,
        'fecha_entrega' => today(),
        'estado' => 'entregue',
        'entregado_por' => $this->user->id,
    ]);

    expect($item->fresh()->stock_total)->toBe(0);
});

it('restores stock when delivery is deleted', function () {
    $item = EpiItem::factory()->withStock(10)->create();

    $entrega = EpiEntrega::create([
        'epi_item_id' => $item->id,
        'colaborador_id' => $this->colaborador->id,
        'cantidad' => 3,
        'fecha_entrega' => today(),
        'estado' => 'entregue',
        'entregado_por' => $this->user->id,
    ]);

    $entrega->delete();

    expect($item->fresh()->stock_total)->toBe(10);
});

// ─────────────────────────────────────────────
// STOCK CRÍTICO
// ─────────────────────────────────────────────

it('detects low stock correctly', function () {
    $item = EpiItem::factory()->create([
        'stock_total' => 1,
        'stock_minimo' => 2,
    ]);

    expect($item->stockBaixo())->toBeTrue();
});

it('does not flag stock as low when above minimum', function () {
    $item = EpiItem::factory()->create([
        'stock_total' => 5,
        'stock_minimo' => 2,
    ]);

    expect($item->stockBaixo())->toBeFalse();
});
