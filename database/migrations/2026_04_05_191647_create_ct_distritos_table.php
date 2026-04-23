<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ct_distritos', function (Blueprint $table) {
            $table->id();
            $table->string('dd', 2)->unique();
            $table->string('desig', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ct_distritos');
    }
};
