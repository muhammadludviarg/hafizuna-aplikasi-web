<div>
    <form wire:submit.prevent="simpan">
        <input type="text" wire:model="nama_kelas" placeholder="Nama Kelas">
        <select wire:model="guru_id">
            @foreach($daftar_guru as $guru)
                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
            @endforeach
        </select>
        <button type="submit">Simpan</button>
    </form>

    <table>
        @foreach($daftar_kelas as $kelas)
            <tr>
                <td>{{ $kelas->nama_kelas }}</td>
                <td>{{ $kelas->guru->nama }}</td>
            </tr>
        @endforeach
    </table>
</div>
