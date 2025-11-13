<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->increments('id_siswa');
            $table->string('nama_siswa', 50);
            $table->string('kode_siswa', 20)->nullable()->unique();
            $table->unsignedInteger('id_kelas')->nullable();
            $table->unsignedInteger('id_ortu');

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')
                ->onDelete('set null');
            $table->foreign('id_ortu')->references('id_ortu')->on('orang_tua')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};