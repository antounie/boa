<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_venta', 20)->unique();
            $table->foreignId('programacion_vuelo_id')->constrained('programacion_vuelos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('cliente_id')->constrained('clientes')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('asiento_id')->constrained('asientos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('transaccion_id')->constrained('transacciones')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('reserva_id')->nullable()->constrained('reservas')->onUpdate('cascade')->onDelete('set null');
            $table->string('metodo_pago', 30);
            $table->decimal('monto_total', 10, 2);
            $table->enum('estado', ['Confirmada', 'Cancelada'])->default('Confirmada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};