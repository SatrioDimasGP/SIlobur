@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Jenis Burung</h1>

    {{-- Tombol kembali --}}
    <div class="mb-3">
        <a href="{{ route('manajemen-lomba.kelola', ['id' => $lomba_id]) }}" class="btn btn-tool">
            <i class="fas fa-arrow-alt-circle-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">Edit Jenis Burung</div>
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
