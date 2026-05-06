<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aeropuertos', function (Blueprint $table) {
            $table->id();
            $table->char('codigo_IATA', 3)->unique();
            $table->string('nombre', 150);
            $table->string('ciudad', 80);
            $table->string('pais', 80);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aeropuertos');
    }
};