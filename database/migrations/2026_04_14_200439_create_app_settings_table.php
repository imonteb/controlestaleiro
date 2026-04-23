<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('chave')->unique();
            $table->text('valor')->nullable();
            $table->string('descricao')->nullable();
            $table->timestamps();
        });

        // Valores predefinidos
        DB::table('app_settings')->insert([
            ['chave' => 'empresa_nome',    'valor' => 'CME',   'descricao' => 'Nome da empresa / empreitada'],
            ['chave' => 'empresa_unidade', 'valor' => 'C016',  'descricao' => 'Unidade / estaleiro'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
