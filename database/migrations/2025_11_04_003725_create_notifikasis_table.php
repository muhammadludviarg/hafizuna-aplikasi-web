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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->foreignId('id_sesi')->constrained('sesi_hafalan', 'id_sesi');
            $table->foreignId('id_ortu')->constrained('orang_tua', 'id_ortu');
            $table->datetime('waktu_kirim')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status', ['terkirim', 'gagal']);
            $table->text('pesan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};