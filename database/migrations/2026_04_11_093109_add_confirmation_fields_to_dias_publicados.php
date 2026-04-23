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
        Schema::table('dias_publicados', function (Blueprint $table) {
            $table->timestamp('confirmado_at')->nullable()->after('activo_tv');
            $table->foreignId('confirmado_por')->nullable()->after('confirmado_at')->constrained('users')->nullOnDelete();
            $table->longText('firma_confirmacao')->nullable()->after('confirmado_por');
        });
    }

    public function down(): void
    {
        Schema::table('dias_publicados', function (Blueprint $table) {
            $table->dropConstrainedForeignId('confirmado_por');
            $table->dropColumn(['confirmado_at', 'firma_confirmacao']);
        });
    }
};
