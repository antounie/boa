<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramo_padre_id')->nullable()->constrained('tramos')->nullOnDelete();
            $table->foreignId('aeropuerto_origen_id')->constrained('aeropuertos');
            $table->foreignId('aeropuerto_destino_id')->constrained('aeropuertos');
            $table->time('duracion_estimada');
            $table->unsignedTinyInteger('orden')->default(1);
            $table->timestamps();
        });

        Schema::create('ruta_tramo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_id')->constrained('rutas')->cascadeOnDelete();
            $table->foreignId('tramo_id')->constrained('tramos')->cascadeOnDelete();
            $table->unsignedTinyInteger('orden')->default(1);
            $table->timestamps();
            $table->unique(['ruta_id', 'tramo_id']);
        });

        // Migrar rutas existentes: cada ruta recibe un tramo directo (sin sub-tramos)
        $rutas = DB::table('rutas')->get();
        foreach ($rutas as $ruta) {
            $tramoId = DB::table('tramos')->insertGetId([
                'tramo_padre_id'        => null,
                'aeropuerto_origen_id'  => $ruta->aeropuerto_origen_id,
                'aeropuerto_destino_id' => $ruta->aeropuerto_destino_id,
                'duracion_estimada'     => $ruta->duracion_estimada,
                'orden'                 => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            DB::table('ruta_tramo')->insert([
                'ruta_id'    => $ruta->id,
                'tramo_id'   => $tramoId,
                'orden'      => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_tramo');
        Schema::dropIfExists('tramos');
    }
};
