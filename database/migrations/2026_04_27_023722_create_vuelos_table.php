<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vuelos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_vuelo', 20)->unique();
            $table->enum('tipo', ['Directo', 'ConEscalas']);
            $table->enum('estado', ['Activo', 'Cancelado'])->default('Activo');
            $table->foreignId('vuelo_padre_id')->nullable()->constrained('vuelos')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vuelos');
    }
};