@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Judul dan Tombol Kembali --}}
        <h1 class="mb-4 d-flex justify-content-between align-items-center">
            <span>Edit Kelas</span>
            <a href="{{ route('manajemen-lomba.kelola', ['id' => $lomba_id]) }}" class="btn btn-tool">
                <i class="fas fa-arrow-alt-circle-left"></i>
            </a>
        </h1>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Form Edit --}}
        <div class="card">
            <div class="card-header">Form Edit Kelas</div>
            <div class="card-body">
                <form
                    action="{{ route('manajemen-lomba.kelola.kelas.update', ['lomba_id' => $lomba_id, 'id' => $kelas->id]) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-2">
                        <label for="kelas">Nama Kelas</label>
                        <input type="text" name="kelas" id="kelas" class="form-control"
                            value="{{ old('kelas', $kelas->nama) }}" required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="harga">Harga</label>
                        <input type="text" name="harga" id="harga" class="form-control"
                            value="{{ old('harga', $kelas->harga) }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
