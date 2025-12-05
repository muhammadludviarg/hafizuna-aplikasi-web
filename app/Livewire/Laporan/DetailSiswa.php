<?php

namespace App\Livewire\Laporan;

use App\Models\Siswa;
use App\Models\Koreksi;
use App\Models\Surah;
use App\Models\TargetHafalanKelompok;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use PDF; // [BARU] Import library PDF

class DetailSiswa extends Component
{
    public Siswa $siswa;
    public $sudahDihapal = [];
    public $belumDihapal = [];
    public $role; // [BARU] Untuk tahu siapa yang lihat

    // [MODIFIKASI] Gunakan Route-Model Binding Laravel.
    // Nama {siswa} di rute akan otomatis inject Model Siswa
    public function mount(Siswa $siswa)
    {
        $this->siswa = $siswa;
        
        if (Auth::guard('admin')->check()) $this->role = 'admin';
        if (Auth::guard('guru')->check()) $this->role = 'guru';
        if (Auth::guard('orangtua')->check()) $this->role = 'orangtua';

        $this->cekOtorisasi();
        $this->loadLaporan();
    }

    protected function cekOtorisasi()
    {
        if ($this->role == 'admin') return; // Admin boleh

        if ($this->role == 'guru') {
            $guru = Auth::guard('guru')->user();
            $isMurid = $guru->kelompok()->whereHas('siswa', fn($q) => $q->where('siswa.id_siswa', $this->siswa->id_siswa))->exists();
            if ($isMurid) return; // Guru boleh
        }
        
        if ($this->role == 'orangtua') {
            $orangTua = Auth::guard('orangtua')->user();
            $isAnak = $orangTua->siswa()->where('id_siswa', $this->siswa->id_siswa)->exists();
            if ($isAnak) return; // Ortu boleh
        }
        
        // Jika tidak lolos
        abort(403, 'Akses ditolak.');
    }

    public function loadLaporan()
    {
        // 1. Ambil Surah yang SUDAH Dihapal (dari tabel Koreksi)
        // (Asumsi FK dan PK sudah benar)
        $this->sudahDihapal = Koreksi::query()
            ->join('sesi_hafalan', 'koreksi.sesi_hafalan_id', '=', 'sesi_hafalan.id_sesi_hafalan')
            ->join('surah', 'sesi_hafalan.id_surah', '=', 'surah.id_surah')
            ->where('sesi_hafalan.id_siswa', $this->siswa->id_siswa)
            ->select(
                'surah.nama_surah',
                'surah.id_surah as surah_id',
                'sesi_hafalan.created_at', // [BARU] Ambil tanggal sesi
                DB::raw('AVG(koreksi.nilai_tajwid) as avg_tajwid'),
                DB::raw('AVG(koreksi.nilai_kelancaran) as avg_kelancaran'),
                DB::raw('AVG(koreksi.nilai_makhroj) as avg_makhroj'),
                DB::raw('AVG(koreksi.nilai_akhir) as avg_rata_rata'),
                DB::raw('COUNT(koreksi.id_koreksi) as total_sesi') // [BARU] Hitung jumlah sesi
            )
            ->groupBy('surah.id_surah', 'surah.nama_surah', 'sesi_hafalan.created_at')
            ->orderBy('sesi_hafalan.created_at', 'desc')
            ->get();

        // 2. Ambil Target Hafalan (dari TargetHafalanKelompok)
        if ($this->siswa->kelompok->isNotEmpty()) { // Siswa bisa punya > 1 kelompok
            $kelompokId = $this->siswa->kelompok->first()->id_kelompok; // Ambil kelompok pertama
            
            $targetSurahIds = TargetHafalanKelompok::query()
                ->where('id_kelompok', $kelompokId)
                ->pluck('id_surah');

            $sudahDihapalIds = $this->sudahDihapal->pluck('surah_id')->unique();

            // 3. Cari Surah yang BELUM Dihapal
            $this->belumDihapal = Surah::query()
                ->whereIn('id_surah', $targetSurahIds)
                ->whereNotIn('id_surah', $sudahDihapalIds)
                ->select('nama_surah', 'jumlah_ayat')
                ->get();
        } else {
            $this->belumDihapal = collect();
        }
    }

    /**
     * [BARU] Fungsi untuk Generate PDF
     */
    public function generatePdf()
    {
        // Muat ulang data untuk memastikan
        $this->loadLaporan(); 

        // Siapkan data untuk dikirim ke view PDF
        $data = [
            'siswa' => $this->siswa,
            'sudahDihapal' => $this->sudahDihapal,
            'belumDihapal' => $this->belumDihapal,
            'tanggal' => now()->format('d/m/Y')
        ];

        // Buat PDF
        $pdf = PDF::loadView('reports.pdf-template', $data);
        
        // Buat nama file yang unik
        $namaFile = 'Laporan_Hafalan_' . $this->siswa->nama . '_' . now()->format('Y-m-d') . '.pdf';
        
        // Download file
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $namaFile);
    }


    public function render()
    {
        // Arahkan ke layout yang sesuai
        return view('livewire.laporan.detail-siswa')
                 ->layout($this->role == 'guru' ? 'layouts.guru' : 'layouts.app');
    }
}