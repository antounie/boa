<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preferencia_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onUpdate('cascade')->onDelete('cascade');
            $table->string('tema', 30)->default('adultos');
            $table->enum('modo_dia_noche', ['Manual', 'Automatico'])->default('Automatico');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preferencia_usuarios');
    }
};