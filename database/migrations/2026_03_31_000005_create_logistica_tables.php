<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 5 — Logística y Activos
 *
 * Tablas: ferramentas, ferramenta_logs, extintores,
 *         auto_socorro_kits, saude_itens, saude_kit_itens
 *
 * Orden de dependencias:
 *   ferramentas (sin FK, referenciada por ferramenta_logs)
 *   ferramenta_logs → ferramentas, colaboradores, vehiculos
 *   extintores → vehiculos, locaciones
 *   auto_socorro_kits → vehiculos, locaciones
 *   saude_itens (sin FK)
 *   saude_kit_itens → auto_socorro_kits, saude_itens
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('ferramentas')) {
            Schema::create('ferramentas', function (Blueprint $table) {
                $table->id();
                $table->string('referencia')->unique();
                $table->string('designacao');
                $table->string('marca')->nullable();
                $table->string('modelo')->nullable();
                $table->string('num_serie')->nullable();
                $table->string('familia')->nullable();
                $table->string('talla')->nullable();
                $table->integer('periodicidade_meses')->default(12);
                $table->string('tipo_documentacao')->default('Manual');
                $table->enum('estado_operacional', ['Apto', 'Não Apto', 'Condicionado', 'Abate'])
                    ->default('Apto');
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ferramenta_logs')) {
            Schema::create('ferramenta_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ferramenta_id')
                    ->constrained('ferramentas')->cascadeOnDelete();
                $table->foreignId('colaborador_id')->nullable()
                    ->constrained('colaboradores');
                $table->foreignId('veiculo_id')->nullable()
                    ->constrained('vehiculos');
                $table->date('data_verificacao')->nullable();
                $table->date('proxima_verificacao')->nullable();
                $table->string('verificado_por')->nullable();
                $table->string('num_registo_verificacao')->nullable();
                $table->json('checklist')->nullable();
                $table->text('observacoes')->nullable();
                $table->integer('folha_id')->nullable();
                $table->boolean('apto')->default(true);
                $table->integer('num_registo_seq')->nullable();
                $table->string('manutencao_tipo')->nullable();
                $table->text('conclusao')->nullable();
                // Assinatura digital (base64 — considerar mover a storage)
                $table->longText('assinatura_path')->nullable();
                $table->string('unidade')->nullable();
                $table->string('tipo_movimento')->default('verificacao');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('extintores')) {
            Schema::create('extintores', function (Blueprint $table) {
                $table->id();
                $table->string('num_serie')->nullable();
                $table->string('qr_code_token')->nullable()->unique();
                $table->string('tipo_agente');
                $table->string('tamanho');
                $table->string('estado')->default('Conforme');
                $table->boolean('needs_restock')->default(false);
                $table->date('data_verificacao')->nullable();
                $table->date('proxima_revisao')->nullable();
                $table->foreignId('veiculo_id')->nullable()
                    ->constrained('vehiculos');
                $table->foreignId('locacion_id')->nullable()
                    ->constrained('locaciones');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('auto_socorro_kits')) {
            Schema::create('auto_socorro_kits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('veiculo_id')->nullable()
                    ->constrained('vehiculos');
                $table->foreignId('locacion_id')->nullable()
                    ->constrained('locaciones');
                $table->string('designacao');
                $table->boolean('needs_restock')->default(false);
                $table->string('identificador_kit')->nullable();
                $table->string('qr_code_token')->nullable()->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('saude_itens')) {
            Schema::create('saude_itens', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('unidade')->default('un');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('saude_kit_itens')) {
            Schema::create('saude_kit_itens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kit_id')
                    ->constrained('auto_socorro_kits')->cascadeOnDelete();
                $table->foreignId('saude_item_id')
                    ->constrained('saude_itens');
                $table->date('data_validade')->nullable();
                $table->integer('quantidade')->default(1);
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('saude_kit_itens');
        Schema::dropIfExists('saude_itens');
        Schema::dropIfExists('auto_socorro_kits');
        Schema::dropIfExists('extintores');
        Schema::dropIfExists('ferramenta_logs');
        Schema::dropIfExists('ferramentas');
        Schema::enableForeignKeyConstraints();
    }
};
