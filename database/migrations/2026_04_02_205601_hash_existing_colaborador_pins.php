<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * 1. Alarga a coluna pin para 255 (necessário para bcrypt hash).
 * 2. Faz hash dos PINs que ainda estão em texto plano.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colaboradores', function (Blueprint $table): void {
            $table->string('pin', 255)->nullable()->change();
        });

        DB::table('colaboradores')
            ->whereNotNull('pin')
            ->orderBy('id')
            ->each(function (object $row): void {
                $pin = $row->pin;

                if (str_starts_with($pin, '$2y$') || str_starts_with($pin, '$argon')) {
                    return; // já está com hash
                }

                DB::table('colaboradores')
                    ->where('id', $row->id)
                    ->update(['pin' => Hash::make($pin)]);
            });
    }

    public function down(): void
    {
        Schema::table('colaboradores', function (Blueprint $table): void {
            $table->string('pin', 10)->nullable()->change();
        });
    }
};
