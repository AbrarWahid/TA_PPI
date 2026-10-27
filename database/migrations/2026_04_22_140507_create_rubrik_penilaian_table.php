<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubrik_penilaian', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rubrik', 20)->unique();
            $table->enum('metode', ['PBL','Test Praktik','Praktik']);
            $table->string('kategori', 50);
            $table->string('aspek', 10);
            $table->string('sub_aspek', 100);
            $table->decimal('bobot_persen', 5, 4);
            $table->json('poin_tersedia');
            $table->json('deskripsi_poin');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubrik_penilaian');
    }
};