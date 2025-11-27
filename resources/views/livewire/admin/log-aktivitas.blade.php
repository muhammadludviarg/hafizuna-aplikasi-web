<div>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Aktivitas Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Nama Pengguna</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Aktivitas</th>
                                    <th scope="col">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $index => $log)
                                    <tr>
                                        {{-- Nomor urut berdasarkan paginasi --}}
                                        <td>{{ $logs->firstItem() + $index }}</td>
                                        <td>{{ $log->nama_lengkap }}</td>
                                        <td>{{ $log->email }}</td>
                                        <td>
                                            @if ($log->aktivitas == 'Login')
                                                <span class="badge bg-success">{{ $log->aktivitas }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $log->aktivitas }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->timestamp->format('d-m-Y H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data log aktivitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link Paginasi --}}
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>