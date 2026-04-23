<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand ENUM → VARCHAR (MySQL uses MODIFY COLUMN, SQLite needs table rebuild)
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: recreate column via schema (already string in SQLite, just ensure default)
            Schema::table('epi_items', function (Blueprint $table) {
                $table->string('tipo', 30)->default('epi')->change();
            });
        } else {
            DB::statement("ALTER TABLE epi_items MODIFY COLUMN tipo VARCHAR(30) NOT NULL DEFAULT 'epi'");
        }

        // Migrate legacy enum values to new string values
        DB::table('epi_items')->where('tipo', 'individual')->update(['tipo' => 'epi']);
        DB::table('epi_items')->where('tipo', 'coletivo')->update(['tipo' => 'epi']);
    }

    public function down(): void
    {
        DB::table('epi_items')->whereIn('tipo', ['epi', 'saude', 'incendio', 'ferramenta'])->update(['tipo' => 'individual']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE epi_items MODIFY COLUMN tipo ENUM('individual','coletivo') NOT NULL DEFAULT 'individual'");
        }
    }
};
