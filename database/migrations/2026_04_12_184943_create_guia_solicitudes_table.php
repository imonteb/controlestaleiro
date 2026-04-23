<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guia_solicitudes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->nullOnDelete();

            // Origem
            $table->enum('origen_tipo', ['frequente', 'ctt'])->default('frequente');
            $table->foreignId('origen_local_id')->nullable()->constrained('locais_frequentes')->nullOnDelete();
            $table->string('origen_dd', 2)->nullable();
            $table->string('origen_cc', 2)->nullable();
            $table->string('origen_localidade', 150)->nullable();
            $table->string('origen_cp4', 4)->nullable();
            $table->string('origen_cp3', 3)->nullable();
            $table->string('origen_morada')->nullable();

            // Destino
            $table->enum('destino_tipo', ['frequente', 'ctt'])->default('ctt');
            $table->foreignId('destino_local_id')->nullable()->constrained('locais_frequentes')->nullOnDelete();
            $table->string('destino_dd', 2)->nullable();
            $table->string('destino_cc', 2)->nullable();
            $table->string('destino_localidade', 150)->nullable();
            $table->string('destino_cp4', 4)->nullable();
            $table->string('destino_cp3', 3)->nullable();
            $table->string('destino_morada')->nullable();

            // Carga
            $table->text('material_descricao');
            $table->text('notas')->nullable();

            // Estado e tramitação
            $table->enum('estado', ['pendente', 'tramitada', 'cancelada'])->default('pendente')->index();
            $table->string('numero_at', 50)->nullable();
            $table->foreignId('processada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processada_at')->nullable();

            $table->timestamps();

            $table->index(['colaborador_id', 'estado']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guia_solicitudes');
    }
};
