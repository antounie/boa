<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programacion_vuelos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vuelo_id')->constrained('vuelos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('ruta_id')->constrained('rutas')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('aeronave_id')->constrained('aeronaves')->onUpdate('cascade')->onDelete('restrict');
            $table->date('fecha_salida');
            $table->time('hora_salida');
            $table->date('fecha_llegada');
            $table->time('hora_llegada');
            $table->decimal('precio_base', 10, 2);
            $table->integer('asientos_vendidos')->default(0);
            $table->enum('estado', ['Programado', 'Completo', 'Salido'])->default('Programado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programacion_vuelos');
    }
};