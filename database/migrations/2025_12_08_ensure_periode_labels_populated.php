<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memastikan semua periode memiliki label yang benar
     */
    public function up(): void
    {
        // Generate label untuk semua periode yang kosong
        DB::statement('
            UPDATE periode 
            SET label = CONCAT(
                "Semester ", semester, " ", tahun_ajaran
            )
            WHERE label IS NULL OR label = "" OR label = "Periode Lama"
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set label ke NULL untuk yang di-generate
        // Note: Tidak bisa di-rollback dengan sempurna karena tidak tahu mana yang di-generate
    }
};
