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
        Schema::create('programacion_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programacion_vuelo_id')->constrained('programacion_vuelos')->cascadeOnDelete();
            $table->foreignId('tipo_clase_id')->constrained('tipo_clases')->cascadeOnDelete();
            $table->decimal('precio', 10, 2);
            $table->timestamps();
            $table->unique(['programacion_vuelo_id', 'tipo_clase_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programacion_precios');
    }
};
