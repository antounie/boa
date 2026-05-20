<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar nuevas columnas como nullable primero
        Schema::table('programacion_vuelos', function (Blueprint $table) {
            $table->string('codigo_vuelo', 20)->nullable()->after('id');
            $table->foreignId('aeropuerto_origen_id')->nullable()->after('ruta_id')
                  ->constrained('aeropuertos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('aeropuerto_destino_id')->nullable()->after('aeropuerto_origen_id')
                  ->constrained('aeropuertos')->onUpdate('cascade')->onDelete('restrict');
        });

        // Poblar codigo_vuelo desde la tabla vuelos
        DB::statement('
            UPDATE programacion_vuelos pv
            JOIN vuelos v ON pv.vuelo_id = v.id
            SET pv.codigo_vuelo = v.codigo_vuelo
        ');

        // Poblar aeropuertos desde la tabla rutas
        DB::statement('
            UPDATE programacion_vuelos pv
            JOIN rutas r ON pv.ruta_id = r.id
            SET pv.aeropuerto_origen_id = r.aeropuerto_origen_id,
                pv.aeropuerto_destino_id = r.aeropuerto_destino_id
        ');

        // Eliminar FK de vuelo_id y la columna
        Schema::table('programacion_vuelos', function (Blueprint $table) {
            $table->dropForeign(['vuelo_id']);
            $table->dropColumn('vuelo_id');
        });

        // Eliminar auto-referencia de vuelos y la tabla vuelos
        Schema::table('vuelos', function (Blueprint $table) {
            $table->dropForeign(['vuelo_padre_id']);
        });

        Schema::dropIfExists('vuelos');
    }

    public function down(): void
    {
        Schema::create('vuelos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_vuelo', 20)->unique();
            $table->enum('tipo', ['Directo', 'ConEscalas']);
            $table->enum('estado', ['Activo', 'Cancelado'])->default('Activo');
            $table->foreignId('vuelo_padre_id')->nullable()->constrained('vuelos')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('programacion_vuelos', function (Blueprint $table) {
            $table->dropForeign(['aeropuerto_origen_id']);
            $table->dropForeign(['aeropuerto_destino_id']);
            $table->dropColumn(['codigo_vuelo', 'aeropuerto_origen_id', 'aeropuerto_destino_id']);
            $table->foreignId('vuelo_id')->after('id')->constrained('vuelos')->onDelete('cascade');
        });
    }
};
