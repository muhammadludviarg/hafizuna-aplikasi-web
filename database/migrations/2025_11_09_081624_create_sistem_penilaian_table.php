<?php
// database/migrations/2025_01_XX_XXXXXX_create_sistem_penilaian_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sistem_penilaian', function (Blueprint $table) {
            $table->id('id_penilaian');
            $table->enum('aspek', ['tajwid', 'makhroj', 'kelancaran']);
            $table->enum('grade', ['A', 'B', 'C']);
            $table->float('proporsi_kesalahan_min')->comment('Proporsi minimal dalam %');
            $table->float('proporsi_kesalahan_max')->comment('Proporsi maksimal dalam %');
            $table->timestamps();
            
            // Index untuk pencarian cepat
            $table->index(['aspek', 'grade']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sistem_penilaian');
    }
};