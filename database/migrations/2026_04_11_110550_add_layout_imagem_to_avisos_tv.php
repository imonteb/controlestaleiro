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
        Schema::table('avisos_tv', function (Blueprint $table) {
            $table->string('layout_imagem', 20)->default('lateral')->after('imagem');
        });
    }

    public function down(): void
    {
        Schema::table('avisos_tv', function (Blueprint $table) {
            $table->dropColumn('layout_imagem');
        });
    }
};
