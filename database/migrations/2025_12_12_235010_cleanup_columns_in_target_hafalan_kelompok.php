<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MATIKAN PROTEKSI DULU
        Schema::disableForeignKeyConstraints();

        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            
            // 1. COPOT FOREIGN KEY 'id_kelompok' (Penyebab Utama Error 1553)
            // Kita lepas dulu supaya dia tidak menghalangi penghapusan index
            try {
                $table->dropForeign(['id_kelompok']); 
            } catch (\Exception $e) {
                // Coba nama manual jika array gagal (jaga-jaga)
                try {
                    $table->dropForeign('target_hafalan_kelompok_id_kelompok_foreign');
                } catch (\Exception $x) {}
            }

            // 2. HAPUS INDEX UNIQUE (Si Gembok)
            // Sekarang harusnya berhasil karena FK yang 'nebeng' sudah dilepas
            try {
                $table->dropUnique('unique_kelompok_periode');
            } catch (\Exception $e) {
                try {
                    $table->dropUnique(['id_kelompok', 'periode']);
                } catch (\Exception $x) {}
            }

            // 3. HAPUS KOLOM SAMPAH (Tujuan Utama)
            if (Schema::hasColumn('target_hafalan_kelompok', 'periode')) {
                $table->dropColumn('periode');
            }
            if (Schema::hasColumn('target_hafalan_kelompok', 'tanggal_mulai')) {
                $table->dropColumn('tanggal_mulai');
            }
            if (Schema::hasColumn('target_hafalan_kelompok', 'tanggal_selesai')) {
                $table->dropColumn('tanggal_selesai');
            }

            // 4. BUAT JEMBATAN BARU (id_periode)
            if (!Schema::hasColumn('target_hafalan_kelompok', 'id_periode')) {
                // Pakai unsignedInteger (sesuai tabel periode kamu yang increments)
                $table->unsignedInteger('id_periode')->nullable()->after('id_kelompok');
                
                $table->foreign('id_periode')
                      ->references('id_periode')
                      ->on('periode')
                      ->onDelete('cascade');
            }

            // 5. PASANG LAGI FOREIGN KEY 'id_kelompok' (Wajib!)
            // Kita kembalikan seperti semula agar relasi antar tabel tetap terjaga
            // Laravel otomatis akan membuatkan index baru khusus buat dia.
            try {
                $table->foreign('id_kelompok')
                      ->references('id_kelompok')
                      ->on('kelompok')
                      ->onDelete('cascade');
            } catch (\Exception $e) {}
        });

        // NYALAKAN PROTEKSI
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            // Rollback: Hapus id_periode
            if (Schema::hasColumn('target_hafalan_kelompok', 'id_periode')) {
                $table->dropForeign(['id_periode']);
                $table->dropColumn('id_periode');
            }

            // Rollback: Kembalikan kolom lama
            if (!Schema::hasColumn('target_hafalan_kelompok', 'periode')) {
                $table->string('periode')->nullable();
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};