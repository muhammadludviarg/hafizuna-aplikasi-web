<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_hafalan_kelompok', function (Blueprint $table) {
            $table->increments('id_target');
            $table->integer('ayat_awal');
            $table->integer('ayat_akhir');
            $table->text('periode')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('id_kelompok');
            $table->unsignedInteger('id_surah');
            $table->unsignedInteger('id_admin');

            $table->foreign('id_kelompok')->references('id_kelompok')->on('kelompok')->onDelete('cascade');
            $table->foreign('id_surah')->references('id_surah')->on('surah')->onDelete('cascade');
            $table->foreign('id_admin')->references('id_admin')->on('admin')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_hafalan_kelompok');
    }
};