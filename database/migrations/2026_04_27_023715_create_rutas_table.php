<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aeropuerto_origen_id')->constrained('aeropuertos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('aeropuerto_destino_id')->constrained('aeropuertos')->onUpdate('cascade')->onDelete('restrict');
            $table->decimal('distancia', 10, 2);
            $table->time('duracion_estimada');
            $table->enum('tipo', ['Nacional', 'Internacional']);
            $table->timestamps();

            $table->unique(['aeropuerto_origen_id', 'aeropuerto_destino_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};