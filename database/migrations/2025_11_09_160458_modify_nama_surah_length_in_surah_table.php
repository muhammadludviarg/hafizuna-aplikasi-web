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
        Schema::table('surah', function (Blueprint $table) {
            // Ubah panjang kolom nama_surah menjadi 100 karakter
            $table->string('nama_surah', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surah', function (Blueprint $table) {
             // Opsional: kembalikan ke kondisi semula jika di-rollback
             // $table->string('nama_surah', 8)->change(); 
        });
    }
};