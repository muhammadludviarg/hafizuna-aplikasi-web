<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LogAktivitas;
use Carbon\Carbon;

class LogAktivitasAdmin extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTanggal = '';
    public $filterAkun = '';
    public $perPage = 10;

    // Untuk modal confirm hapus
    public $showDeleteModal = false;
    public $deleteOptionDays = 90;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTanggal()
    {
        $this->resetPage();
    }

    public function updatingFilterAkun()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterTanggal = '';
        $this->filterAkun = '';
        $this->resetPage();
    }

    // FUNGSI HAPUS LOG LAMA
    public function openDeleteModal()
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteOptionDays = 90;
    }

    public function cleanOldLogs()
    {
        try {
            $days = $this->deleteOptionDays;
            $cutoffDate = Carbon::now()->subDays($days);
            
            // Hitung yang akan dihapus
            $count = LogAktivitas::where('timestamp', '<', $cutoffDate)->count();
            
            if ($count === 0) {
                session()->flash('message', '✅ Tidak ada log yang perlu dihapus (lebih dari ' . $days . ' hari).');
                $this->closeDeleteModal();
                return;
            }
            
            // Hapus log
            $deleted = LogAktivitas::where('timestamp', '<', $cutoffDate)->delete();
            
            session()->flash('message', "✅ Berhasil menghapus {$deleted} log aktivitas yang lebih dari {$days} hari.");
            $this->closeDeleteModal();
            $this->resetPage();
            
        } catch (\Exception $e) {
            session()->flash('error', '❌ Gagal menghapus log: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = LogAktivitas::with('akun')
            ->when($this->search, function($q) {
                $q->where('aktivitas', 'like', '%' . $this->search . '%')
                  ->orWhereHas('akun', function($query) {
                      $query->where('nama_lengkap', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->filterTanggal, function($q) {
                $q->whereDate('timestamp', $this->filterTanggal);
            })
            ->when($this->filterAkun, function($q) {
                $q->where('id_akun', $this->filterAkun);
            })
            ->orderBy('timestamp', 'desc');

        $logs = $query->paginate($this->perPage);

        // Get unique users for filter
        $users = LogAktivitas::with('akun')
            ->select('id_akun')
            ->distinct()
            ->get()
            ->pluck('akun')
            ->filter()
            ->sortBy('nama_lengkap');

        // Statistik
        $today = Carbon::today();
        $statsToday = LogAktivitas::whereDate('timestamp', $today)->count();
        $statsWeek = LogAktivitas::whereBetween('timestamp', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        $statsMonth = LogAktivitas::whereMonth('timestamp', Carbon::now()->month)
            ->whereYear('timestamp', Carbon::now()->year)
            ->count();
        
        // Hitung log yang akan dihapus per opsi
        $willDelete30 = LogAktivitas::where('timestamp', '<', Carbon::now()->subDays(30))->count();
        $willDelete90 = LogAktivitas::where('timestamp', '<', Carbon::now()->subDays(90))->count();
        $willDelete180 = LogAktivitas::where('timestamp', '<', Carbon::now()->subDays(180))->count();

        return view('livewire.admin.log-aktivitas-admin', [
            'logs' => $logs,
            'users' => $users,
            'statsToday' => $statsToday,
            'statsWeek' => $statsWeek,
            'statsMonth' => $statsMonth,
            'willDelete30' => $willDelete30,
            'willDelete90' => $willDelete90,
            'willDelete180' => $willDelete180,
        ])->layout('layouts.app');
    }
}