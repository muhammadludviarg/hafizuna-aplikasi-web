<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // TAHAP 1: Cabut semua Foreign Key yang mengganggu
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            // Cabut FK id_kelompok agar kita bisa menghapus Unique Index
            $table->dropForeign(['id_kelompok']);

            // Cabut FK id_periode lama agar tidak terjadi "Duplicate Key 121" saat dipasang ulang
            $table->dropForeign(['id_periode']);
        });

        // TAHAP 2: Sekarang aman untuk menghapus Unique Index
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->dropUnique('unique_kelompok_periode');
        });

        // TAHAP 3: Modifikasi Kolom
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            // Hapus kolom periode lama
            if (Schema::hasColumn('target_hafalan_kelompok', 'periode')) {
                $table->dropColumn('periode');
            }

            // Ubah id_periode agar tidak boleh null
            $table->unsignedInteger('id_periode')->nullable(false)->change();
        });

        // TAHAP 4: Pasang kembali semua Foreign Key
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            // Kembalikan FK id_kelompok
            $table->foreign('id_kelompok')
                ->references('id_kelompok')
                ->on('kelompok')
                ->onDelete('cascade');

            // Kembalikan FK id_periode
            $table->foreign('id_periode')
                ->references('id_periode')
                ->on('periode')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        // Hapus FK saat rollback
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->dropForeign(['id_kelompok']);
            $table->dropForeign(['id_periode']);
        });

        // Kembalikan struktur kolom
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->unsignedInteger('id_periode')->nullable()->change();

            if (!Schema::hasColumn('target_hafalan_kelompok', 'periode')) {
                $table->string('periode')->nullable()->after('id_kelompok');
            }
        });

        // Pasang kembali FK dan Aturan Unique yang lama
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->unique(['id_kelompok', 'periode'], 'unique_kelompok_periode');

            $table->foreign('id_kelompok')
                ->references('id_kelompok')
                ->on('kelompok')
                ->onDelete('cascade');

            $table->foreign('id_periode')
                ->references('id_periode')
                ->on('periode')
                ->onDelete('cascade');
        });
    }
};