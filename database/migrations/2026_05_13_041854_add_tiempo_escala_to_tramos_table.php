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
        Schema::table('tramos', function (Blueprint $table) {
            $table->time('tiempo_escala')->nullable()->after('duracion_estimada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tramos', function (Blueprint $table) {
            $table->dropColumn('tiempo_escala');
        });
    }
};
