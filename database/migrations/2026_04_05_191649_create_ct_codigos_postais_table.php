<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ct_codigos_postais', function (Blueprint $table) {
            $table->id();
            $table->string('dd', 2);
            $table->string('cc', 2);
            $table->string('llll', 4);
            $table->string('localidade', 150);
            $table->string('art_cod', 20)->nullable();
            $table->string('art_tipo', 30)->nullable();
            $table->string('pri_prep', 10)->nullable();
            $table->string('art_titulo', 30)->nullable();
            $table->string('seg_prep', 10)->nullable();
            $table->string('art_desig', 200)->nullable();
            $table->string('art_local', 150)->nullable();
            $table->string('troco', 200)->nullable();
            $table->string('porta', 20)->nullable();
            $table->string('cliente', 255)->nullable();
            $table->string('cp4', 4);
            $table->string('cp3', 3);
            $table->string('cpalf', 150);
            $table->string('nome_arteria', 255)->nullable();

            $table->index(['dd', 'cc']);
            $table->index('cp4');
            $table->index('localidade');
        });

        // Fulltext index — MySQL only (SQLite does not support it)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE ct_codigos_postais ADD FULLTEXT ft_codigos_postais (nome_arteria, localidade, cpalf)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ct_codigos_postais');
    }
};
