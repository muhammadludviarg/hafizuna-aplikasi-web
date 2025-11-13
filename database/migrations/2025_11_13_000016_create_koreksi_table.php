<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('koreksi', function (Blueprint $table) {
            $table->increments('id_koreksi');
            $table->unsignedInteger('id_sesi');
            $table->unsignedInteger('id_ayat');
            $table->integer('kata_ke');
            $table->enum('kategori_kesalahan', ['tajwid', 'makhroj', 'kelancaran']);
            $table->text('catatan');

            $table->foreign('id_sesi')->references('id_sesi')->on('sesi_hafalan')->onDelete('cascade');
            $table->foreign('id_ayat')->references('id_ayat')->on('ayat')->onDelete('cascade');

            $table->index('id_sesi'); // Sesuai SQL
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koreksi');
    }
};