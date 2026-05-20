<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rol_permisos', function (Blueprint $table) {
            $table->foreignId('permiso_id')->nullable()->after('rol_id')
                  ->constrained('permisos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('rol_permisos', function (Blueprint $table) {
            $table->dropForeign(['permiso_id']);
            $table->dropColumn('permiso_id');
        });
    }
};
