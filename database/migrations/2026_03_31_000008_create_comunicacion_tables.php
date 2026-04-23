<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 8 — Comunicación y Administración
 *
 * Tablas: avisos_tv, app_notifications, guia_transportes,
 *         guia_items, legal_pages
 *
 * Orden de dependencias:
 *   avisos_tv (sin FK)
 *   app_notifications → colaboradores
 *   guia_transportes → users, colaboradores
 *   guia_items → guia_transportes
 *   legal_pages → users
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('avisos_tv')) {
            Schema::create('avisos_tv', function (Blueprint $table) {
                $table->id();
                $table->string('titulo');
                $table->text('conteudo')->nullable();
                $table->string('imagem')->nullable();
                $table->string('cor', 20)->default('azul');
                $table->boolean('ativo')->default(true);
                $table->smallInteger('ordem')->unsigned()->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('app_notifications')) {
            Schema::create('app_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('colaborador_id')->nullable()
                    ->constrained('colaboradores')->cascadeOnDelete();
                $table->string('tipo')->default('geral');
                $table->string('titulo');
                $table->text('mensagem');
                $table->dateTime('data_expiracao')->nullable();
                $table->boolean('activa')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('guia_transportes')) {
            Schema::create('guia_transportes', function (Blueprint $table) {
                $table->id();
                $table->string('tipo')->default('normal'); // normal|global
                // Datos de origen (carga)
                $table->string('local_carga_nome')->nullable();
                $table->string('local_carga_morada')->nullable();
                $table->string('local_carga_localidade')->nullable();
                $table->string('local_carga_cpostal')->nullable();
                $table->date('data_inicio')->nullable();
                $table->time('hora_inicio')->nullable();
                $table->string('matricula')->nullable();
                // Datos de destino
                $table->string('destino_morada')->nullable();
                $table->string('destino_localidade')->nullable();
                $table->string('destino_cpostal')->nullable();
                $table->date('data_fim')->nullable();
                $table->time('hora_fim')->nullable();
                // Relaciones
                $table->foreignId('user_id')->nullable()
                    ->constrained('users')->nullOnDelete();
                $table->foreignId('requerente_id')->nullable()
                    ->constrained('colaboradores')->index();
                $table->foreignId('processed_by_id')->nullable()
                    ->constrained('users');
                // Estado y control
                $table->string('status')->default('pendente')->index(); // pendente|aprovado|recusado
                $table->string('numero_oficial')->nullable()->index();
                $table->text('motivo_recusa')->nullable();
                $table->timestamp('data_emissao')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('guia_items')) {
            Schema::create('guia_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guia_transporte_id')
                    ->constrained('guia_transportes')->cascadeOnDelete();
                $table->string('descricao');
                $table->decimal('quantidade', 10, 2)->default(0);
                $table->string('unidade')->default('und');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('legal_pages')) {
            Schema::create('legal_pages', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('titulo');
                $table->longText('conteudo');
                $table->string('versao');
                $table->date('ultima_revisao');
                $table->boolean('publicada')->default(true);
                $table->foreignId('updated_by')->nullable()
                    ->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('legal_pages');
        Schema::dropIfExists('guia_items');
        Schema::dropIfExists('guia_transportes');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('avisos_tv');
        Schema::enableForeignKeyConstraints();
    }
};
