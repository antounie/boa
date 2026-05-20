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
        Schema::table('programacion_vuelos', function (Blueprint $table) {
            $table->date('fecha_original')->nullable()->after('hora_llegada');
            $table->time('hora_original')->nullable()->after('fecha_original');
            $table->text('motivo_reprogramacion')->nullable()->after('hora_original');
        });
    }

    public function down(): void
    {
        Schema::table('programacion_vuelos', function (Blueprint $table) {
            $table->dropColumn(['fecha_original', 'hora_original', 'motivo_reprogramacion']);
        });
    }
};
