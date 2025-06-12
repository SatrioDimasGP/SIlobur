@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Penugasan Juri</h1>

    {{-- Menampilkan semua pesan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- Menampilkan pesan success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <!-- Tombol Aksi Tambah -->
    <form method="POST" action="{{ route('penjadwalan-juri.store') }}">
        @csrf

        <div class="card mb-4">
            <div class="card-header">Penugasan Juri Baru</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="lomba">Lomba:</label>
                    <select id="lomba-select" name="lomba_id" class="form-control" required>
                        <option value="">-- Pilih Lomba --</option>
                        @foreach($lombas as $lomba)
                            <option value="{{ $lomba->id }}">{{ $lomba->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="jenis_burung_kelas">Jenis Burung & Kelas:</label>
                    <select id="burung-kelas-select" name="burung_id" class="form-control" required>
                        <option value="">-- Pilih Jenis Burung & Kelas --</option>
                        {{-- Akan diisi melalui JavaScript --}}
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="juri">Juri:</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">-- Pilih Juri --</option>
                        @foreach($juris as $juri)
                            <option value="{{ $juri->id }}">{{ $juri->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mt-2">Simpan</button>
            </div>
        </div>
    </form>

    <!-- Tabel Daftar Penugasan -->
    <h5 class="mb-3">Daftar Penugasan Juri</h5>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Lomba</th>
                        <th>Juri</th>
                        <th>Blok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penugasanJuri as $index => $penugasan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $penugasan->lomba->nama }}</td>
                            <td>{{ $penugasan->user->name }}</td>
                            <td>{{ $penugasan->blok->nama }}</td>
                            <td>
                                <a href="{{ route('penjadwalan-juri.edit', $penugasan->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('penjadwalan-juri.destroy', $penugasan->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data penugasan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript untuk konfirmasi hapus -->
<script>
    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus penugasan ini?");
    }
</script>

<!-- JavaScript untuk mengisi dropdown -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lombaSelect = document.getElementById('lomba-select');
    const burungKelasSelect = document.getElementById('burung-kelas-select');

    lombaSelect.addEventListener('change', function () {
        const lombaId = this.value;
        burungKelasSelect.innerHTML = '<option value="">-- Memuat data... --</option>';

        if (lombaId) {
            fetch(`/penjadwalan-juri/get-jenis-burung-kelas/${lombaId}`)
                .then(response => response.json())
                .then(data => {
                    burungKelasSelect.innerHTML = '<option value="">-- Pilih Jenis Burung & Kelas --</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.text = item.label;
                        burungKelasSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    burungKelasSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                    console.error(error);
                });
        } else {
            burungKelasSelect.innerHTML = '<option value="">-- Pilih Jenis Burung & Kelas --</option>';
        }
    });
});
</script>

@endsection
