<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogAktivitas;
use Carbon\Carbon;

class CleanOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean 
                            {--days=90 : Hapus log lebih dari X hari}
                            {--force : Skip konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus log aktivitas yang sudah lama (default: > 90 hari)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $force = $this->option('force');

        // Hitung tanggal batas
        $cutoffDate = Carbon::now()->subDays($days);

        // Hitung jumlah log yang akan dihapus
        $count = LogAktivitas::where('timestamp', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('✅ Tidak ada log yang perlu dihapus.');
            return 0;
        }

        // Tampilkan info
        $this->info("📊 Ditemukan {$count} log lebih dari {$days} hari.");
        $this->info("📅 Log sebelum {$cutoffDate->format('d M Y H:i')} akan dihapus.");

        // Konfirmasi (kecuali pakai --force)
        if (!$force && !$this->confirm('⚠️  Lanjutkan hapus log?', false)) {
            $this->warn('❌ Dibatalkan.');
            return 1;
        }

        // Hapus log
        $this->info('🗑️  Menghapus log...');
        
        $deleted = LogAktivitas::where('timestamp', '<', $cutoffDate)->delete();

        $this->info("✅ Berhasil menghapus {$deleted} log aktivitas.");
        
        return 0;
    }
}