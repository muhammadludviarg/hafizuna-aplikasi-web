<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sesi_hafalan', function (Blueprint $table) {
            $table->id('id_sesi');
            $table->foreignId('id_siswa')->constrained('siswa', 'id_siswa');
            $table->foreignId('id_guru')->constrained('guru', 'id_guru');
            $table->foreignId('id_surah_mulai')->constrained('surah', 'id_surah');
            $table->integer('ayat_mulai');
            $table->foreignId('id_surah_selesai')->constrained('surah', 'id_surah');
            $table->integer('ayat_selesai');
            $table->datetime('tanggal_setor')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->float('nilai_tajwid')->nullable();
            $table->float('nilai_makhroj')->nullable();
            $table->float('nilai_kelancaran')->nullable();
            $table->float('nilai_rata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_hafalan');
    }
};