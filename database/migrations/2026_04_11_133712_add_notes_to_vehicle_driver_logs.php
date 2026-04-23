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
        Schema::table('vehicle_driver_logs', function (Blueprint $table) {
            $table->string('takeover_from_name')->nullable()->after('ended_at');
            $table->timestamp('takeover_at')->nullable()->after('takeover_from_name');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_driver_logs', function (Blueprint $table) {
            $table->dropColumn(['takeover_from_name', 'takeover_at']);
        });
    }
};
