<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {

            // Hapus kolom yang sudah tidak terpakai
            if (Schema::hasColumn('target_hafalan_kelompok', 'periode')) {
                $table->dropColumn('periode');
            }
            if (Schema::hasColumn('target_hafalan_kelompok', 'tanggal_mulai')) {
                $table->dropColumn('tanggal_mulai');
            }
            if (Schema::hasColumn('target_hafalan_kelompok', 'tanggal_selesai')) {
                $table->dropColumn('tanggal_selesai');
            }

            // TIDAK PERLU LAGI MENGHAPUS/MENAMBAH FOREIGN KEY ATAU INDEX
            // KARENA SUDAH DISELESAIKAN OLEH FILE MIGRASI SEBELUMNYA
        });
    }

    public function down(): void
    {
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            // Rollback: Kembalikan kolom lama jika di-rollback
            if (!Schema::hasColumn('target_hafalan_kelompok', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
            }
        });
    }
};