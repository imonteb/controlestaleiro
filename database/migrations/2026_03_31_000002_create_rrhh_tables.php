<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 2 — RRHH Base
 *
 * Tablas: locaciones, tipos_trabajo, colaboradores, peps
 *
 * Orden de dependencias:
 *   locaciones ← peps
 *   tipos_trabajo ← peps
 *   colaboradores (sin FK, referenciada por muchos)
 *   peps → locaciones, tipos_trabajo
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('locaciones')) {
            Schema::create('locaciones', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tipos_trabajo')) {
            Schema::create('tipos_trabajo', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->string('color')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('colaboradores')) {
            Schema::create('colaboradores', function (Blueprint $table) {
                $table->id();
                $table->string('numero_colaborador')->unique();
                $table->string('nombre');
                $table->string('apellido');
                $table->string('telefono')->nullable();
                $table->string('pin', 4)->nullable();
                $table->timestamp('terms_accepted_at')->nullable();
                $table->string('denominacion_cargo');
                $table->boolean('activo')->default(true);
                $table->boolean('visible_en_dashboard')->default(true);
                $table->text('motivo_baja')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('peps')) {
            Schema::create('peps', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->foreignId('locacion_id')->nullable()
                    ->constrained('locaciones')->nullOnDelete();
                $table->foreignId('tipo_trabajo_id')->nullable()
                    ->constrained('tipos_trabajo')->nullOnDelete();
                $table->boolean('activo')->default(true);
                $table->text('motivo_baja')->nullable();
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('peps');
        Schema::dropIfExists('colaboradores');
        Schema::dropIfExists('tipos_trabajo');
        Schema::dropIfExists('locaciones');
        Schema::enableForeignKeyConstraints();
    }
};
