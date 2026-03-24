<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_pagos', function (Blueprint $table) {
            $table->string('estatus', 5)->default('A')->change();
        });
    }

    public function down(): void
    {
        Schema::table('cond_pagos', function (Blueprint $table) {
            $table->char('estatus', 1)->default('A')->change();
        });
    }
};
