<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('koreksi', function (Blueprint $table) {
            $table->id('id_koreksi');
            $table->foreignId('id_sesi')->constrained('sesi_hafalan', 'id_sesi')->onDelete('cascade');
            $table->foreignId('id_ayat')->constrained('ayat', 'id_ayat');
            $table->integer('kata_ke');
            $table->text('kata_arab')->nullable();
            $table->enum('jenis_kesalahan', ['tajwid','makhroj','panjang','lafal']);
            $table->text('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koreksi');
    }
};