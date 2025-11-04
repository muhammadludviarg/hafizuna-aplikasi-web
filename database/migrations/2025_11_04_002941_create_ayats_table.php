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
        Schema::create('ayat', function (Blueprint $table) {
            $table->id('id_ayat');
            $table->foreignId('id_surah')->constrained('surah', 'id_surah');
            $table->integer('nomor_ayat');
            $table->text('teks_arab')->nullable();
            $table->text('terjemahan')->nullable();
            $table->unique(['id_surah', 'nomor_ayat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayat');
    }
};