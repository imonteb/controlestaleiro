<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guia_transportes', function (Blueprint $table) {
            // Origin: created by admin directly or requested by a worker
            $table->string('origem')->default('admin')->after('tipo'); // admin|colaborador

            // CT cascade fields — district/municipality codes for UI cascade + repeat
            $table->string('local_carga_dd', 2)->nullable()->after('local_carga_cpostal');
            $table->string('local_carga_cc', 2)->nullable()->after('local_carga_dd');

            // Destination name (mirrors local_carga_nome for origin)
            $table->string('destino_nome')->nullable()->after('matricula');
            $table->string('destino_dd', 2)->nullable()->after('destino_cpostal');
            $table->string('destino_cc', 2)->nullable()->after('destino_dd');

            // Official AT number (replaces numero_oficial)
            $table->string('numero_at')->nullable()->after('numero_oficial');
        });

        // Rename status → estado
        Schema::table('guia_transportes', function (Blueprint $table) {
            $table->renameColumn('status', 'estado');
        });

        // Map old status values to new estado values
        DB::table('guia_transportes')->where('estado', 'processada')->update(['estado' => 'emitida']);
        DB::table('guia_transportes')->where('estado', 'aprovado')->update(['estado' => 'emitida']);

        // Copy numero_oficial → numero_at for existing records
        DB::table('guia_transportes')
            ->whereNotNull('numero_oficial')
            ->where('numero_oficial', '!=', '')
            ->update(['numero_at' => DB::raw('numero_oficial')]);

        // All existing records were worker-requested (requerente_id is set)
        DB::table('guia_transportes')
            ->whereNotNull('requerente_id')
            ->update(['origem' => 'colaborador']);

        // Drop guia_solicitudes — absorbed into guia_transportes
        Schema::dropIfExists('guia_solicitudes');
    }

    public function down(): void
    {
        Schema::create('guia_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->cascadeOnDelete();
            $table->string('estado')->default('pendente');
            $table->timestamps();
        });

        DB::table('guia_transportes')->where('estado', 'emitida')->update(['estado' => 'processada']);

        Schema::table('guia_transportes', function (Blueprint $table) {
            $table->renameColumn('estado', 'status');
            $table->dropColumn([
                'origem',
                'local_carga_dd', 'local_carga_cc',
                'destino_nome', 'destino_dd', 'destino_cc',
                'numero_at',
            ]);
        });
    }
};
