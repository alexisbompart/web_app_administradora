<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('afilapto', function (Blueprint $table) {
            $table->foreignId('apartamento_id')->nullable()->change();
            $table->foreignId('edificio_id')->nullable()->change();
            $table->foreignId('compania_id')->nullable()->change();
            $table->date('fecha_afiliacion')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('afilapto', function (Blueprint $table) {
            $table->foreignId('apartamento_id')->nullable(false)->change();
            $table->foreignId('edificio_id')->nullable(false)->change();
            $table->foreignId('compania_id')->nullable(false)->change();
            $table->date('fecha_afiliacion')->nullable(false)->change();
        });
    }
};
