<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 7 — Seguridad y Emergencias
 *
 * Tablas: emergency_contacts, emergency_procedures, incident_reports
 *
 * emergency_contacts y emergency_procedures son independientes
 * incident_reports → colaboradores
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('emergency_contacts')) {
            Schema::create('emergency_contacts', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('telefone');
                $table->string('descricao')->nullable();
                $table->boolean('activo')->default(true);
                $table->integer('ordem')->default(0);
                $table->string('logo')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('emergency_procedures')) {
            Schema::create('emergency_procedures', function (Blueprint $table) {
                $table->id();
                $table->string('titulo');
                $table->string('subtitulo')->nullable();
                $table->text('conteudo');
                $table->string('tipo')->default('geral');
                $table->boolean('activo')->default(true);
                $table->integer('ordem')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('incident_reports')) {
            Schema::create('incident_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('colaborador_id')
                    ->constrained('colaboradores')->cascadeOnDelete();
                $table->string('tipo');
                $table->dateTime('data_hora');
                $table->string('localizacao');
                $table->text('descricao');
                $table->string('photo_path')->nullable();
                $table->string('estado')->default('novo');
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('incident_reports');
        Schema::dropIfExists('emergency_procedures');
        Schema::dropIfExists('emergency_contacts');
        Schema::enableForeignKeyConstraints();
    }
};
