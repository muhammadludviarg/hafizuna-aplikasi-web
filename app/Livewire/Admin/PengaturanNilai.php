<?php
// app/Livewire/Admin/PengaturanNilai.php

namespace App\Livewire\Admin;

use App\Models\SistemPenilaian;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class PengaturanNilai extends Component
{
    // Data untuk semua aspek dan grade
    public $settings = [];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $aspekList = ['kelancaran', 'tajwid', 'makhroj'];
        $gradeList = ['A', 'B', 'C'];
        
        foreach ($aspekList as $aspek) {
            foreach ($gradeList as $grade) {
                $setting = SistemPenilaian::where('aspek', $aspek)
                    ->where('grade', $grade)
                    ->first();
                
                if ($setting) {
                    $this->settings[$aspek][$grade] = [
                        'id' => $setting->id_penilaian,
                        'proporsi_min' => $setting->proporsi_kesalahan_min,
                        'proporsi_max' => $setting->proporsi_kesalahan_max,
                    ];
                }
            }
        }
    }

    public function simpanPengaturan()
    {
        // Validasi
        $this->validate([
            'settings.*.*.proporsi_min' => 'required|numeric|min:0|max:100',
            'settings.*.*.proporsi_max' => 'required|numeric|min:0|max:100|gte:settings.*.*.proporsi_min',
        ]);

        // =======================================================
        // ▼▼▼ TAMBAHKAN LOGIKA PENGECEKAN BENTROK DI SINI ▼▼▼
        // =======================================================
        foreach ($this->settings as $aspek => $grades) {
            // $grades akan berisi ['A' => [...], 'B' => [...], 'C' => [...]]
            
            // Ambil semua data rentang untuk aspek ini
            $ranges = [];
            foreach ($grades as $grade => $data) {
                $ranges[$grade] = [
                    'min' => (float) $data['proporsi_min'],
                    'max' => (float) $data['proporsi_max']
                ];
            }

            // Bandingkan setiap grade dengan grade lainnya (A vs B, A vs C, B vs C)
            $gradesList = array_keys($ranges); // ['A', 'B', 'C']

            for ($i = 0; $i < count($gradesList); $i++) {
                for ($j = $i + 1; $j < count($gradesList); $j++) {
                    
                    $grade1 = $gradesList[$i]; // misal 'A'
                    $grade2 = $gradesList[$j]; // misal 'B'

                    $range1 = $ranges[$grade1];
                    $range2 = $ranges[$grade2];

                    // Rumus Pengecekan Overlap (Bentrok)
                    $isOverlapping = ($range1['min'] < $range2['max']) && ($range1['max'] > $range2['min']);

                    if ($isOverlapping) {
                        // BENTROK! Hentikan proses simpan dan kirim pesan error
                        throw ValidationException::withMessages([
                            'bentrok' => "Error di aspek '{$aspek}': Rentang {$grade1} ({$range1['min']}-{$range1['max']}) bentrok dengan rentang {$grade2} ({$range2['min']}-{$range2['max']})."
                        ]);
                    }
                }
            }
        }

        // Simpan ke database
        foreach ($this->settings as $aspek => $grades) {
            foreach ($grades as $grade => $data) {
                SistemPenilaian::where('id_penilaian', $data['id'])
                    ->update([
                        'proporsi_kesalahan_min' => $data['proporsi_min'],
                        'proporsi_kesalahan_max' => $data['proporsi_max'],
                    ]);
            }
        }

        session()->flash('success', 'Pengaturan nilai berhasil disimpan!');
    }

    public function resetKeDefault()
    {
        $defaultSettings = [
            'kelancaran' => [
                'A' => ['proporsi_min' => 0, 'proporsi_max' => 5],
                'B' => ['proporsi_min' => 5, 'proporsi_max' => 10],
                'C' => ['proporsi_min' => 10, 'proporsi_max' => 100],
            ],
            'tajwid' => [
                'A' => ['proporsi_min' => 0, 'proporsi_max' => 3],
                'B' => ['proporsi_min' => 3, 'proporsi_max' => 7],
                'C' => ['proporsi_min' => 7, 'proporsi_max' => 100],
            ],
            'makhroj' => [
                'A' => ['proporsi_min' => 0, 'proporsi_max' => 3],
                'B' => ['proporsi_min' => 3, 'proporsi_max' => 7],
                'C' => ['proporsi_min' => 7, 'proporsi_max' => 100],
            ],
        ];

        // Update ke database DAN sekaligus update state lokal
        foreach ($defaultSettings as $aspek => $grades) {
            foreach ($grades as $grade => $data) {
                if (isset($this->settings[$aspek][$grade]['id'])) {
                    // Update database
                    SistemPenilaian::where('id_penilaian', $this->settings[$aspek][$grade]['id'])
                        ->update([
                            'proporsi_kesalahan_min' => $data['proporsi_min'],
                            'proporsi_kesalahan_max' => $data['proporsi_max'],
                        ]);
                }
            }
        }
        
        // Muat ulang pengaturan dari database
        $this->loadSettings();

        session()->flash('success', 'Pengaturan berhasil direset ke nilai default!');
    }

    public function render()
    {
        return view('livewire.admin.pengaturan-nilai')
            ->layout('layouts.admin'); 
    }
}