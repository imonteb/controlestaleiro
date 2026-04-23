<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 3 — Planificación y Vehículos
 *
 * Tablas: vehiculos, asignaciones, asignacion_colaborador,
 *         asignacion_vehiculo, dias_publicados, vehicle_driver_logs
 *
 * Orden de dependencias:
 *   vehiculos (sin FK, referenciada por muchos)
 *   asignaciones → peps
 *   asignacion_colaborador → asignaciones, colaboradores
 *   asignacion_vehiculo → asignaciones, vehiculos
 *   dias_publicados (sin FK)
 *   vehicle_driver_logs → vehiculos, colaboradores
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('vehiculos')) {
            Schema::create('vehiculos', function (Blueprint $table) {
                $table->id();
                $table->string('marca');
                $table->string('modelo');
                $table->string('matricula')->unique();
                $table->boolean('activo')->default(true);
                $table->text('motivo_baja')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('asignaciones')) {
            Schema::create('asignaciones', function (Blueprint $table) {
                $table->id();
                $table->date('fecha');
                $table->foreignId('pep_id')->nullable()
                    ->constrained('peps')->cascadeOnDelete();
                $table->string('estado')->default('estaleiro');
                $table->text('notas')->nullable();
                // Campos de evento especial (consulta médica, formación, etc.)
                $table->dateTime('fecha_hora_evento')->nullable();
                $table->string('descripcion_evento')->nullable();
                $table->string('nombre_taller', 150)->nullable();
                $table->date('fecha_entrada_taller')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('asignacion_colaborador')) {
            Schema::create('asignacion_colaborador', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asignacion_id')
                    ->constrained('asignaciones')->cascadeOnDelete();
                $table->foreignId('colaborador_id')
                    ->constrained('colaboradores')->cascadeOnDelete();
                $table->string('rol_en_equipo')->nullable();
                $table->string('equipo_tipo')->default('principal')
                    ->comment('principal o auxiliar');
                $table->boolean('es_jefe')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('asignacion_vehiculo')) {
            Schema::create('asignacion_vehiculo', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asignacion_id')
                    ->constrained('asignaciones')->cascadeOnDelete();
                $table->foreignId('vehiculo_id')
                    ->constrained('vehiculos')->cascadeOnDelete();
                $table->string('equipo_tipo')->default('principal')
                    ->comment('principal o auxiliar');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('dias_publicados')) {
            Schema::create('dias_publicados', function (Blueprint $table) {
                $table->id();
                $table->date('fecha')->unique();
                $table->boolean('activo_tv')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('vehicle_driver_logs')) {
            Schema::create('vehicle_driver_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehicle_id')
                    ->constrained('vehiculos')->cascadeOnDelete();
                $table->foreignId('colaborador_id')
                    ->constrained('colaboradores')->cascadeOnDelete();
                $table->timestamp('started_at');
                $table->timestamp('ended_at')->nullable();
                $table->timestamps();

                $table->index(['vehicle_id', 'started_at']);
                $table->index(['colaborador_id', 'started_at']);
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vehicle_driver_logs');
        Schema::dropIfExists('dias_publicados');
        Schema::dropIfExists('asignacion_vehiculo');
        Schema::dropIfExists('asignacion_colaborador');
        Schema::dropIfExists('asignaciones');
        Schema::dropIfExists('vehiculos');
        Schema::enableForeignKeyConstraints();
    }
};
