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
        // TAHAP 1: Hapus Foreign Key lama agar kolom id_periode bisa diedit
        // Kita menggunakan try-catch atau pengecekan agar tidak error jika FK belum ada
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            // Hapus constraint foreign key yang menyebabkan error 1832
            // Nama constraint diambil dari pesan error Anda sebelumnya
            $table->dropForeign('target_hafalan_kelompok_id_periode_foreign');
        });

        // TAHAP 2: Modifikasi Kolom dan Hapus kolom lama
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            // Hapus kolom 'periode' (string) jika masih ada
            if (Schema::hasColumn('target_hafalan_kelompok', 'periode')) {
                $table->dropColumn('periode');
            }

            // Ubah tipe data id_periode.
            // PENTING: Gunakan unsignedInteger karena tabel 'periode' memakai int(10), bukan bigint.
            $table->unsignedInteger('id_periode')->nullable(false)->change();
        });

        // TAHAP 3: Pasang kembali Foreign Key yang baru
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->foreign('id_periode')
                ->references('id_periode')
                ->on('periode')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus FK baru
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->dropForeign(['id_periode']);
        });

        // Kembalikan struktur kolom (opsional, tergantung kebutuhan rollback)
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            // Kembalikan jadi nullable
            $table->unsignedInteger('id_periode')->nullable()->change();

            // Tambah kolom periode lagi (jika perlu rollback penuh)
            if (!Schema::hasColumn('target_hafalan_kelompok', 'periode')) {
                $table->string('periode')->nullable()->after('id_kelompok');
            }
        });

        // Pasang kembali FK lama (opsional)
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->foreign('id_periode')
                ->references('id_periode')
                ->on('periode')
                ->onDelete('cascade');
        });
    }
};