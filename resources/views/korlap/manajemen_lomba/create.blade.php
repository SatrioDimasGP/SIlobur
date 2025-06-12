@extends('layouts.app')

@section('title', 'Tambah Lomba')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Tambah Lomba Baru</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"></ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <div class="card-tools">
                    <a href="{{ route('manajemen-lomba.index') }}" class="btn btn-tool">
                        <i class="fas fa-arrow-alt-circle-left"></i>
                    </a>
                </div>
            </div>
            <form action="{{ route('manajemen-lomba.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lomba</label>
                        <input type="text" name="nama" id="nama" class="form-control" required value="{{ old('nama') }}">
                    </div>

                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" class="form-control" required value="{{ old('lokasi') }}">
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal & Waktu Lomba</label>
                        <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" required value="{{ old('tanggal') }}">
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status_lomba_id" class="form-label">Status Lomba</label>
                        <select name="status_lomba_id" id="status_lomba_id" class="form-control" required>
                            <option value="1" {{ old('status_lomba_id') == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="2" {{ old('status_lomba_id') == 2 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
