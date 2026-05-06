<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asiento_programacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asiento_id')->constrained('asientos')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('programacion_vuelo_id')->constrained('programacion_vuelos')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('estado', ['Disponible', 'Ocupado', 'Bloqueado'])->default('Disponible');
            $table->timestamps();

            $table->unique(['asiento_id', 'programacion_vuelo_id']);
        });

        // Quitar el campo estado de la tabla asientos
        Schema::table('asientos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }

    public function down(): void
    {
        Schema::table('asientos', function (Blueprint $table) {
            $table->enum('estado', ['Disponible', 'Ocupado', 'Bloqueado'])->default('Disponible');
        });

        Schema::dropIfExists('asiento_programacion');
    }
};