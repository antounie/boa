<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tripulaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programacion_vuelo_id')->constrained('programacion_vuelos')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('empleado_id')->constrained('empleados')->onUpdate('cascade')->onDelete('restrict');
            $table->enum('cargo', ['Piloto', 'Copiloto', 'Auxiliar']);
            $table->timestamps();

            $table->unique(['programacion_vuelo_id', 'empleado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tripulaciones');
    }
};
