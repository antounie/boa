<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aeronave_id')->constrained('aeronaves')->onUpdate('cascade')->onDelete('cascade');
            $table->string('numero', 10);
            $table->integer('fila');
            $table->foreignId('tipo_clase_id')->constrained('tipo_clases')->onUpdate('cascade')->onDelete('restrict');
            $table->enum('estado', ['Disponible', 'Ocupado', 'Bloqueado'])->default('Disponible');
            $table->timestamps();

            $table->unique(['aeronave_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asientos');
    }
};