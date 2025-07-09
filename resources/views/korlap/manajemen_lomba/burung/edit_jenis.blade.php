@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Judul dan Tombol Kembali --}}
    <h1 class="mb-4 d-flex justify-content-between align-items-center">
        <span>Edit Jenis Burung</span>
        <a href="{{ route('manajemen-lomba.kelola', ['id' => $lomba_id]) }}" class="btn btn-tool">
            <i class="fas fa-arrow-alt-circle-left"></i>
        </a>
    </h1>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Form Edit --}}
    <div class="card">
        <div class="card-header">Form Edit Jenis Burung</div>
        <div class="card-body">
            <form action="{{ route('manajemen-lomba.kelola.burung.jenis-burung.update', ['lomba_id' => $lomba_id, 'id' => $jenisBurung->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-2">
                    <label for="jenis_burung">Nama Jenis Burung</label>
                    <input type="text" name="jenis_burung" id="jenis_burung" class="form-control" value="{{ old('jenis_burung', $jenisBurung->nama) }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
