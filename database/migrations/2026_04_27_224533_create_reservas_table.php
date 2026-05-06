<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_reserva', 20)->unique();
            $table->foreignId('programacion_vuelo_id')->constrained('programacion_vuelos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('cliente_id')->constrained('clientes')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('asiento_id')->constrained('asientos')->onUpdate('cascade')->onDelete('restrict');
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['Confirmada', 'Cancelada'])->default('Confirmada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};