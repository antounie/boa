<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salida_id')->unique()->constrained('salidas')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('programacion_vuelo_id')->constrained('programacion_vuelos')->onUpdate('cascade')->onDelete('restrict');
            $table->decimal('monto_total', 12, 2);
            $table->integer('cantidad_pasajes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};