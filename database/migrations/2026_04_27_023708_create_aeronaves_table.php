<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aeronaves', function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 20)->unique();
            $table->string('modelo', 80);
            $table->string('fabricante', 80);
            $table->integer('capacidad_total');
            $table->enum('estado', ['Activa', 'Inactiva'])->default('Activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aeronaves');
    }
};