<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok', function (Blueprint $table) {
            $table->increments('id_kelompok');
            $table->string('tahun_ajaran', 50)->comment('nama_kelompok'); // Sesuai SQL
            $table->unsignedInteger('id_kelas');
            $table->unsignedInteger('id_guru');

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')
                  ->onDelete('cascade');
            $table->foreign('id_guru')->references('id_guru')->on('guru')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok');
    }
};