<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ct_concelhos', function (Blueprint $table) {
            $table->id();
            $table->string('dd', 2);
            $table->string('cc', 2);
            $table->string('desig', 150);
            $table->foreign('dd')->references('dd')->on('ct_distritos')->onDelete('cascade');
            $table->unique(['dd', 'cc']);
            $table->index('dd');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ct_concelhos');
    }
};
