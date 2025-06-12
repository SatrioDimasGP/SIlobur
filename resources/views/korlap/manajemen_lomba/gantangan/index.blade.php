@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Manajemen Gantangan</h1>

    {{-- Tombol kembali --}}
    <div class="mb-3 d-flex justify-content-end">
        <a href="{{ route('manajemen-lomba.kelola', $lomba_id) }}" class="btn btn-tool">
            <i class="fas fa-arrow-alt-circle-left"></i>
        </a>
    </div>

    <!-- Notifikasi -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Form Tambah Gantangan -->
    <div class="card mb-4">
        <div class="card-header">Tambah Gantangan</div>
        <div class="card-body">
            <form action="{{ route('manajemen-lomba.kelola.gantangan.store', $lomba_id) }}" method="POST">
                @csrf
                <div class="mb-3 row">
                    <div class="col">
                        <label for="nomor_awal" class="form-label">Nomor Awal</label>
                        <input type="number" name="nomor_awal" id="nomor_awal" class="form-control" required>
                    </div>
                    <div class="col">
                        <label for="nomor_akhir" class="form-label">Nomor Akhir (Kosongkan untuk 1 nomor)</label>
                        <input type="number" name="nomor_akhir" id="nomor_akhir" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Gantangan -->
    <div class="card">
        <div class="card-header">Daftar Gantangan</div>
        <div class="card-body">
            @if ($gantangans->isEmpty())
                <p class="text-center">Belum ada data gantangan. Silakan tambahkan gantangan terlebih dahulu.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nomor Gantangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gantangans as $gantangan)
                            <tr>
                                <td>{{ $gantangan->nomor }}</td>
                                <td>
                                    <a href="{{ route('manajemen-lomba.kelola.gantangan.edit', ['lomba_id' => $lomba_id, 'id' => $gantangan->id]) }}"class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('manajemen-lomba.kelola.gantangan.destroy', [$lomba_id, $gantangan->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
