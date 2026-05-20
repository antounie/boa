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
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('pasajero_nombre')->nullable()->after('estado');
            $table->string('pasajero_apellido')->nullable()->after('pasajero_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['pasajero_nombre', 'pasajero_apellido']);
        });
    }
};
