<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode', function (Blueprint $table) {
            $table->increments('id_periode');
            $table->string('tahun_ajaran', 9); // Format: 2025/2026
            $table->tinyInteger('semester'); // 1 atau 2
            $table->string('label'); // Semester 1 2025/2026
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            
            // UNIQUE constraint pada (tahun_ajaran, semester)
            $table->unique(['tahun_ajaran', 'semester'], 'unique_tahun_semester');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode');
    }
};
