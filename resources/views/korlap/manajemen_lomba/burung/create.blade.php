@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 d-flex justify-content-between align-items-center">
        <span>Tambah Gabungan Jenis Burung & Kelas</span>
        <a href="{{ route('manajemen-lomba.kelola', $lomba->id) }}" class="btn btn-tool">
            <i class="fas fa-arrow-alt-circle-left"></i>
        </a>
    </h1>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Form Tambah Gabungan --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Form Tambah Gabungan</span>
        </div>
        <div class="card-body">
            <form action="{{ route('manajemen-lomba.kelola.burung.store', ['lomba_id' => $lomba->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis_burung_id">Jenis Burung</label>
                        <select name="jenis_burung_id" id="jenis_burung_id" class="form-control" required>
                            <option value="">-- Pilih Jenis Burung --</option>
                            @foreach($jenisBurungs as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kelas_id">Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Gabungan</button>
            </form>
        </div>
    </div>

    {{-- Tabel Gabungan --}}
    <div class="card">
        <div class="card-header">Daftar Gabungan Jenis Burung & Kelas</div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Jenis Burung</th>
                        <th>Kelas</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($burungGabungan as $bg)
                        <tr>
                            <td>{{ $bg->jenisBurung->nama }}</td>
                            <td>{{ $bg->kelas->nama }}</td>
                            <td>
                                <a href="{{ route('manajemen-lomba.kelola.burung.edit', ['lomba_id' => $lomba->id, 'burung_id' => $bg->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('manajemen-lomba.kelola.burung.destroy', ['lomba_id' => $lomba->id, 'burung_id' => $bg->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus gabungan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Belum ada gabungan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
