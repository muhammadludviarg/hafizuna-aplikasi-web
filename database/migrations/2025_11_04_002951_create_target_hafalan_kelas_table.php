<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('target_hafalan_kelas', function (Blueprint $table) {
            $table->id('id_target');
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas');
            $table->foreignId('id_surah')->constrained('surah', 'id_surah');
            $table->integer('ayat_awal');
            $table->integer('ayat_akhir');
            $table->text('periode')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_hafalan_kelas');
    }
};