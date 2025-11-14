<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siswa_kelompok', function (Blueprint $table) {
            $table->increments('id_siswa_kelompok');
            $table->unsignedInteger('id_siswa');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->unsignedInteger('id_kelompok');

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('id_kelompok')->references('id_kelompok')->on('kelompok')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_kelompok');
    }
};