<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ayat', function (Blueprint $table) {
            $table->increments('id_ayat');
            $table->unsignedInteger('id_surah');
            $table->integer('nomor_ayat');
            $table->text('teks_arab')->nullable();
            $table->text('terjemahan')->nullable();
            $table->integer('jumlah_kata');

            $table->foreign('id_surah')->references('id_surah')->on('surah')
                ->onDelete('cascade');
            $table->unique(['id_surah', 'nomor_ayat']); // Constraint unique ganda
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayat');
    }
};