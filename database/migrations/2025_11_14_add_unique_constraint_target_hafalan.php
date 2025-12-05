<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->unique(['id_kelompok', 'periode'], 'unique_kelompok_periode');
        });
    }

    public function down(): void
    {
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->dropUnique('unique_kelompok_periode');
        });
    }
};
