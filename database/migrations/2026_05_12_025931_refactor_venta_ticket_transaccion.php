<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar asiento_id a tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('asiento_id')->nullable()->after('venta_id')
                  ->constrained('asientos')->onUpdate('cascade')->onDelete('restrict');
        });

        // 2. Agregar venta_id a transacciones
        Schema::table('transacciones', function (Blueprint $table) {
            $table->foreignId('venta_id')->nullable()->after('id')
                  ->constrained('ventas')->onUpdate('cascade')->onDelete('cascade');
        });

        // 3. Migrar datos: ticket hereda asiento_id desde su venta
        DB::statement('
            UPDATE tickets t
            JOIN ventas v ON t.venta_id = v.id
            SET t.asiento_id = v.asiento_id
        ');

        // 4. Migrar datos: transaccion apunta a su venta
        DB::statement('
            UPDATE transacciones tr
            JOIN ventas v ON v.transaccion_id = tr.id
            SET tr.venta_id = v.id
        ');

        // 5. Quitar unique de tickets.venta_id (permite múltiples tickets por venta)
        // En MySQL hay que soltar el FK primero, luego el unique, luego recrear el FK sin unique
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropUnique(['venta_id']);
            $table->foreign('venta_id')->references('id')->on('ventas')->onUpdate('cascade')->onDelete('cascade');
        });

        // 6. Eliminar columnas obsoletas de ventas
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['asiento_id']);
            $table->dropForeign(['transaccion_id']);
            $table->dropColumn(['asiento_id', 'transaccion_id', 'metodo_pago']);
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('asiento_id')->nullable()->constrained('asientos');
            $table->foreignId('transaccion_id')->nullable()->constrained('transacciones');
            $table->string('metodo_pago', 30)->nullable();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->unique('venta_id');
            $table->dropForeign(['asiento_id']);
            $table->dropColumn('asiento_id');
        });

        Schema::table('transacciones', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropColumn('venta_id');
        });
    }
};
