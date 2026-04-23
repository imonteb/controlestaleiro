<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 4 — EPIs (Equipos de Protección Individual)
 *
 * Tablas: epi_items, epi_recepciones, epi_entregas,
 *         epi_ajustes, epi_pedidos
 *
 * Orden de dependencias:
 *   epi_items (sin FK, referenciada por todas)
 *   epi_recepciones → epi_items, users
 *   epi_entregas → epi_items, colaboradores, users
 *   epi_ajustes → epi_items, users
 *   epi_pedidos → colaboradores, epi_items
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('epi_items')) {
            Schema::create('epi_items', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('talla')->nullable();
                $table->string('codigo')->nullable();
                $table->enum('tipo', ['individual', 'coletivo'])->default('individual');
                $table->text('descripcion')->nullable();
                $table->boolean('requiere_talla')->default(false);
                $table->json('tallas_disponibles')->nullable();
                $table->json('campos_personalizados')->nullable();
                // Riscos associados (array de 1-23 tipos de risco)
                $table->json('riscos')->nullable();
                $table->string('ca_numero')->nullable();
                $table->integer('stock_total')->default(0);
                $table->json('stock_por_talla')->nullable();
                $table->string('unidade', 30)->default('unidade');
                $table->unsignedInteger('stock_minimo')->default(0);
                $table->boolean('activo')->default(true);
                $table->string('motivo_baja')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('epi_recepciones')) {
            Schema::create('epi_recepciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('epi_item_id')
                    ->constrained('epi_items')->cascadeOnDelete();
                $table->unsignedInteger('cantidad');
                $table->string('talla')->nullable();
                $table->date('fecha');
                $table->string('proveedor')->nullable();
                $table->string('numero_factura')->nullable();
                $table->text('observaciones')->nullable();
                $table->foreignId('registrado_por')
                    ->constrained('users');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('epi_entregas')) {
            Schema::create('epi_entregas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('epi_item_id')
                    ->constrained('epi_items')->cascadeOnDelete();
                $table->foreignId('colaborador_id')->nullable()
                    ->constrained('colaboradores');
                $table->unsignedInteger('cantidad')->default(1);
                $table->string('talla')->nullable();
                $table->json('valores_personalizados')->nullable();
                $table->date('fecha_entrega');
                $table->date('fecha_devolucion')->nullable();
                $table->enum('estado', ['entregue', 'devolvido', 'danificado', 'perdido'])
                    ->default('entregue');
                // Firmas digitales (base64 — considerar mover a storage en producción)
                $table->longText('firma')->nullable();
                $table->longText('firma_devolucion')->nullable();
                $table->text('observaciones')->nullable();
                $table->foreignId('entregado_por')
                    ->constrained('users');
                $table->timestamps();

                $table->index(['epi_item_id', 'estado']);
                $table->index(['colaborador_id', 'fecha_entrega']);
            });
        }

        if (! Schema::hasTable('epi_ajustes')) {
            Schema::create('epi_ajustes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('epi_item_id')
                    ->constrained('epi_items')->cascadeOnDelete();
                $table->string('talla')->nullable();
                $table->integer('stock_sistema');
                $table->integer('stock_real');
                $table->integer('diferencia');
                $table->text('motivo');
                $table->foreignId('ajustado_por')
                    ->constrained('users');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('epi_pedidos')) {
            Schema::create('epi_pedidos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('colaborador_id')
                    ->constrained('colaboradores')->cascadeOnDelete();
                $table->foreignId('epi_item_id')
                    ->constrained('epi_items')->cascadeOnDelete();
                $table->string('tamanho')->nullable();
                $table->integer('quantidade')->default(1);
                $table->string('estado')->default('pendente');
                $table->text('motivo_colaborador')->nullable();
                $table->text('motivo_admin')->nullable();
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('epi_pedidos');
        Schema::dropIfExists('epi_ajustes');
        Schema::dropIfExists('epi_entregas');
        Schema::dropIfExists('epi_recepciones');
        Schema::dropIfExists('epi_items');
        Schema::enableForeignKeyConstraints();
    }
};
