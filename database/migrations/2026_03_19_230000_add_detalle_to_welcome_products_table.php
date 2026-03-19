<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('welcome_products', function (Blueprint $table) {
            $table->longText('detalle')->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('welcome_products', function (Blueprint $table) {
            $table->dropColumn('detalle');
        });
    }
};
