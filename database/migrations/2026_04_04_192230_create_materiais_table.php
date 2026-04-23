<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiais', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nome');
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('material_categorias')
                ->nullOnDelete();
            $table->string('unidade_padrao', 20)->default('UND');
            $table->boolean('activo')->default(true);
            $table->string('motivo_baja')->nullable();
            $table->timestamps();

            $table->index(['activo', 'categoria_id']);
            $table->index('nome');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiais');
    }
};
