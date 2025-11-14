<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_hafalan', function (Blueprint $table) {
            $table->increments('id_sesi');
            $table->unsignedInteger('id_siswa');
            $table->unsignedInteger('id_surah_mulai');
            $table->integer('ayat_mulai');
            $table->unsignedInteger('id_surah_selesai');
            $table->integer('ayat_selesai');
            $table->datetime('tanggal_setor')->useCurrent();
            $table->float('proporsi_tajwid');
            $table->float('proporsi_makhroj');
            $table->float('proporsi_kelancaran');
            $table->integer('skor_tajwid');
            $table->integer('skor_makhroj');
            $table->integer('skor_kelancaran');
            $table->string('grade_tajwid', 2);
            $table->string('grade_makhroj', 2);
            $table->string('grade_kelancaran', 2);
            $table->float('nilai_rata');
            $table->unsignedInteger('id_guru');

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('id_surah_mulai')->references('id_surah')->on('surah')->onDelete('restrict');
            $table->foreign('id_surah_selesai')->references('id_surah')->on('surah')->onDelete('restrict');
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('restrict');
            
            $table->index('id_siswa'); // Sesuai SQL
            $table->index('id_surah_mulai'); // Sesuai SQL
            $table->index('id_surah_selesai'); // Sesuai SQL
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_hafalan');
    }
};