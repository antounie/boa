<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->timestamp('bloqueado_hasta')->nullable()->after('intentos_fallidos');
            $table->integer('veces_bloqueado')->default(0)->after('intentos_fallidos');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['bloqueado_hasta', 'veces_bloqueado']);
        });
    }
};
