<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominatim_cache', function (Blueprint $table) {
            $table->id();

            // Clave de búsqueda: hash(query_normalizado + dd + cc)
            $table->string('search_hash', 64)->unique();
            $table->string('query_text');

            // Contexto administrativo (de ct_distritos / ct_concelhos)
            $table->string('dd', 2)->nullable()->index();
            $table->string('cc', 2)->nullable();

            // Datos de dirección (mapeados a campos de guias_transporte)
            $table->string('road')->nullable();
            $table->string('suburb')->nullable();
            $table->string('localidade')->nullable();
            $table->string('postcode', 10)->nullable();

            // Coordenadas
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lon', 10, 7)->nullable();

            // OpenStreetMap reference
            $table->string('osm_type', 1)->nullable(); // N, W, R
            $table->unsignedBigInteger('osm_id')->nullable();

            // Gestión del caché
            $table->unsignedInteger('hit_count')->default(1);
            $table->string('source', 20)->default('nominatim');
            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();

            $table->index(['dd', 'cc']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominatim_cache');
    }
};
