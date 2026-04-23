<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locais_frequentes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->enum('tipo', ['portugal', 'internacional'])->default('portugal');
            $table->string('morada', 255)->nullable();
            $table->string('localidade', 150)->nullable();
            $table->string('cp4', 4)->nullable();
            $table->string('cp3', 3)->nullable();
            $table->string('cpalf', 150)->nullable();
            $table->foreignId('codigo_postal_id')->nullable()->constrained('ct_codigos_postais')->nullOnDelete();
            $table->string('pais', 100)->default('Portugal');
            $table->boolean('activo')->default(true);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('tipo');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locais_frequentes');
    }
};
