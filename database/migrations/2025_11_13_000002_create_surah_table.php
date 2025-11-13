<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surah', function (Blueprint $table) {
            $table->increments('id_surah');
            $table->integer('nomor_surah')->unique();
            $table->string('nama_surah', 100); // Sesuai SQL Anda
            $table->integer('jumlah_ayat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surah');
    }
};