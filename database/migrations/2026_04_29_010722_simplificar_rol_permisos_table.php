<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rol_permisos', function (Blueprint $table) {
            $table->dropForeign(['permiso_id']);
            $table->dropColumn(['permiso_id', 'crear', 'leer', 'actualizar', 'eliminar']);
            $table->boolean('acceso')->default(false)->after('tabla');
        });
    }

    public function down(): void
    {
        Schema::table('rol_permisos', function (Blueprint $table) {
            $table->dropColumn('acceso');
            $table->foreignId('permiso_id')->nullable()->constrained('permisos');
            $table->boolean('crear')->default(false);
            $table->boolean('leer')->default(false);
            $table->boolean('actualizar')->default(false);
            $table->boolean('eliminar')->default(false);
        });
    }
};