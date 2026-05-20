<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programacion_vuelos', function (Blueprint $table) {
            $table->foreignId('ruta_tramo_id')->nullable()->after('ruta_id')->constrained('ruta_tramo');
        });

        // Poblar ruta_tramo_id a partir del ruta_id existente
        DB::statement('
            UPDATE programacion_vuelos pv
            JOIN ruta_tramo rt ON rt.ruta_id = pv.ruta_id
            SET pv.ruta_tramo_id = rt.id
        ');

        Schema::table('programacion_vuelos', function (Blueprint $table) {
            $table->foreignId('ruta_tramo_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('programacion_vuelos', function (Blueprint $table) {
            $table->dropForeign(['ruta_tramo_id']);
            $table->dropColumn('ruta_tramo_id');
        });
    }
};
