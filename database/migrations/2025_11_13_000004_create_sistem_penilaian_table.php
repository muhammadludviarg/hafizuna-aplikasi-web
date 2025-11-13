<?php
// database/migrations/2025_11_13_000004_create_sistem_penilaian_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Saya ubah $table->id() menjadi $table->increments() 
        // agar konsisten dengan int(11) di file SQL Anda
        Schema::create('sistem_penilaian', function (Blueprint $table) {
            $table->increments('id_penilaian'); 
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