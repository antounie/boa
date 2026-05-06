<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onUpdate('cascade')->onDelete('cascade');
            $table->string('tabla', 80);
            $table->boolean('crear')->default(false);
            $table->boolean('leer')->default(false);
            $table->boolean('actualizar')->default(false);
            $table->boolean('eliminar')->default(false);
            $table->timestamps();

            $table->unique(['rol_id', 'permiso_id', 'tabla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_permisos');
    }
};