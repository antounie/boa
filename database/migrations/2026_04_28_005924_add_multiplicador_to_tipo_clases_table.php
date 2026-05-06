<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_clases', function (Blueprint $table) {
            $table->decimal('multiplicador_precio', 4, 2)->default(1.00)->after('nombre');
        });

        // Actualizar multiplicadores por defecto
        DB::table('tipo_clases')->where('nombre', 'Económica')->update(['multiplicador_precio' => 1.00]);
        DB::table('tipo_clases')->where('nombre', 'Ejecutiva')->update(['multiplicador_precio' => 1.50]);
        DB::table('tipo_clases')->where('nombre', 'Primera Clase')->update(['multiplicador_precio' => 2.50]);
    }

    public function down(): void
    {
        Schema::table('tipo_clases', function (Blueprint $table) {
            $table->dropColumn('multiplicador_precio');
        });
    }
};