<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->unsignedInteger('id_periode')->nullable()->after('periode');
            $table->foreign('id_periode')
                ->references('id_periode')
                ->on('periode')
                ->onDelete('cascade');
        });

        // Migrate existing periode text data to periode table
        $uniquePeriodes = DB::table('target_hafalan_kelompok')
            ->whereNotNull('periode')
            ->distinct()
            ->pluck('periode')
            ->toArray();

        foreach ($uniquePeriodes as $periodeText) {
            // Parse periode text (format: "Semester 1 2025/2026")
            preg_match('/Semester (\d+) (\d{4}\/\d{4})/', $periodeText, $matches);
            
            if (!empty($matches)) {
                $semester = (int)$matches[1];
                $tahunAjaran = $matches[2];
                
                $periodeId = DB::table('periode')->insertGetId([
                    'tahun_ajaran' => $tahunAjaran,
                    'semester' => $semester,
                    'label' => $periodeText,
                    'is_active' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update target_hafalan_kelompok dengan id_periode
                DB::table('target_hafalan_kelompok')
                    ->where('periode', $periodeText)
                    ->update(['id_periode' => $periodeId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('target_hafalan_kelompok', function (Blueprint $table) {
            $table->dropForeign(['id_periode']);
            $table->dropColumn('id_periode');
        });
    }
};
