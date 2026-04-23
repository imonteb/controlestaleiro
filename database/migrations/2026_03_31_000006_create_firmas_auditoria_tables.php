<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 6 — Firmas Digitales y Auditoría de Sesiones
 *
 * Tablas: signature_requests, user_sessions
 *
 * signature_requests usa relación polimórfica (signable_type/signable_id)
 * compatible con: User, FerramentaLog, EpiEntrega
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('signature_requests')) {
            Schema::create('signature_requests', function (Blueprint $table) {
                $table->id();
                $table->string('token')->unique();
                // Firma base64 (considerar mover a storage/app/signatures/ en producción)
                $table->longText('signature_data')->nullable();
                $table->string('status')->default('pending'); // pending|completed|expired
                // Relación polimórfica: User, FerramentaLog, EpiEntrega
                $table->string('signable_type');
                $table->unsignedBigInteger('signable_id');
                $table->json('metadata')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('expires_at')->nullable();  // Default: 24h
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['signable_type', 'signable_id']);
            });
        }

        if (! Schema::hasTable('user_sessions')) {
            Schema::create('user_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')
                    ->constrained('users')->cascadeOnDelete();
                $table->string('session_id')->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                // Geolocalización opcional (IP lookup)
                $table->string('city')->nullable();
                $table->string('region')->nullable();
                $table->string('country')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamp('login_at');
                $table->timestamp('last_activity_at')->index();
                $table->timestamp('logout_at')->nullable()->index();
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('signature_requests');
        Schema::enableForeignKeyConstraints();
    }
};
