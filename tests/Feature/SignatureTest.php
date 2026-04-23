<?php

use App\Models\EpiEntrega;
use App\Models\SignatureRequest;
use App\Models\User;
use App\Services\SignatureService;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    $this->service = app(SignatureService::class);
    $this->user = User::factory()->create(['role' => ['epi']]);
});

// ─────────────────────────────────────────────
// SignatureService
// ─────────────────────────────────────────────

it('stores a base64 signature to disk and returns a path', function () {
    $base64 = 'data:image/png;base64,'.base64_encode('fake-image-data');

    $path = $this->service->store($base64);

    expect($path)->toStartWith('signatures/')
        ->and($path)->toEndWith('.png');

    Storage::disk('local')->assertExists($path);
});

it('returns the same path if already stored', function () {
    $base64 = base64_encode('fake-image-data');
    $firstPath = $this->service->store('data:image/png;base64,'.$base64);

    $secondPath = $this->service->store($firstPath);

    expect($secondPath)->toBe($firstPath);
    Storage::disk('local')->assertExists($firstPath);
});

it('converts a stored path back to a base64 data URI', function () {
    $originalBase64 = 'data:image/png;base64,'.base64_encode('fake-image-data');
    $path = $this->service->store($originalBase64);

    $result = $this->service->toBase64($path);

    expect($result)->toStartWith('data:image/png;base64,');
});

it('returns raw base64 unchanged when not a stored path', function () {
    $base64 = 'data:image/png;base64,'.base64_encode('raw-data');

    $result = $this->service->toBase64($base64);

    expect($result)->toBe($base64);
});

it('returns null when stored file no longer exists', function () {
    $result = $this->service->toBase64('signatures/nonexistent.png');

    expect($result)->toBeNull();
});

it('deletes a stored signature file', function () {
    $path = $this->service->store('data:image/png;base64,'.base64_encode('data'));

    Storage::disk('local')->assertExists($path);

    $this->service->delete($path);

    Storage::disk('local')->assertMissing($path);
});

// ─────────────────────────────────────────────
// SignatureController (HTTP)
// ─────────────────────────────────────────────

it('completes a signature request and saves to storage', function () {
    $colaborador = \App\Models\Colaborador::factory()->create();
    $epiItem = \App\Models\EpiItem::factory()->withStock(5)->create();

    $entrega = EpiEntrega::create([
        'epi_item_id' => $epiItem->id,
        'colaborador_id' => $colaborador->id,
        'cantidad' => 1,
        'fecha_entrega' => today(),
        'estado' => 'entregue',
        'entregado_por' => $this->user->id,
    ]);

    $signRequest = SignatureRequest::create([
        'token' => 'test-token-abc',
        'status' => 'pending',
        'signable_type' => EpiEntrega::class,
        'signable_id' => $entrega->id,
        'expires_at' => now()->addHour(),
    ]);

    $base64 = 'data:image/png;base64,'.base64_encode('firma-data');

    $response = $this->postJson('/sign/test-token-abc', ['signature' => $base64]);

    $response->assertOk()->assertJson(['success' => true]);

    $signRequest->refresh();
    expect($signRequest->status)->toBe('completed')
        ->and($signRequest->signature_data)->toStartWith('signatures/');

    Storage::disk('local')->assertExists($signRequest->signature_data);
});

it('rejects an expired signature token', function () {
    $signRequest = SignatureRequest::create([
        'token' => 'expired-token',
        'status' => 'expired',
        'signable_type' => User::class,
        'signable_id' => $this->user->id,
        'expires_at' => now()->subHour(),
    ]);

    $response = $this->postJson('/sign/expired-token', [
        'signature' => 'data:image/png;base64,abc',
    ]);

    $response->assertForbidden();
});

it('rejects signing an already completed request', function () {
    $signRequest = SignatureRequest::create([
        'token' => 'done-token',
        'status' => 'completed',
        'signable_type' => User::class,
        'signable_id' => $this->user->id,
        'expires_at' => now()->addHour(),
    ]);

    $response = $this->postJson('/sign/done-token', [
        'signature' => 'data:image/png;base64,abc',
    ]);

    $response->assertForbidden();
});
