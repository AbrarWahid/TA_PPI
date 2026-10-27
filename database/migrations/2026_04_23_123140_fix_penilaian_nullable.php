<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->date('tanggal_penilaian')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->date('tanggal_penilaian')->nullable(false)->change();
            $table->string('status')->nullable(false)->change();
        });
    }
};