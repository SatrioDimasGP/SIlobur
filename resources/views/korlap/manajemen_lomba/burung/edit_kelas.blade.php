@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Edit Kelas</h1>

        {{-- Tombol kembali --}}
        <div class="mb-3">
            <a href="{{ route('manajemen-lomba.kelola', ['id' => $lomba_id]) }}" class="btn btn-tool">
                <i class="fas fa-arrow-alt-circle-left"></i> Kembali
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">Edit Kelas</div>
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
