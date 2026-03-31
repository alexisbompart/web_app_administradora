<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('afilapto', function (Blueprint $table) {
            $table->string('cod_pint', 20)->nullable()->after('compania_id');
        });
    }

    public function down(): void
    {
        Schema::table('afilapto', function (Blueprint $table) {
            $table->dropColumn('cod_pint');
        });
    }
};
