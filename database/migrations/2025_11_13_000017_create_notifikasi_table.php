<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->increments('id_notifikasi');
            $table->datetime('waktu_kirim')->useCurrent();
            $table->enum('status', ['terkirim', 'gagal']);
            $table->text('pesan');
            $table->unsignedInteger('id_ortu');
            $table->unsignedInteger('id_sesi');

            $table->foreign('id_ortu')->references('id_ortu')->on('orang_tua')->onDelete('cascade');
            $table->foreign('id_sesi')->references('id_sesi')->on('sesi_hafalan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};