<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_tramo_id')->nullable()->after('asiento_id');
            $table->foreign('sub_tramo_id')->references('id')->on('tramos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['sub_tramo_id']);
            $table->dropColumn('sub_tramo_id');
        });
    }
};
