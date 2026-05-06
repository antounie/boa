<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('apellido', 80);
            $table->string('documento_identidad', 20)->unique();
            $table->date('fecha_nacimiento');
            $table->string('email', 100);
            $table->string('telefono', 20)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};