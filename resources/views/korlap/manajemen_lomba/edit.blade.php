@extends('layouts.app')

@section('title', 'Edit Lomba')

@section('content')
<div class="container mt-4">
    <div class="content-header px-2">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Edit Lomba</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                </ol>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="m-0"></h5>
            <div class="card-tools">
                <a href="{{ route('manajemen-lomba.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
            </div>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('manajemen-lomba.update', $lomba->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lomba</label>
                    <input type="text" name="nama" id="nama" class="form-control" required value="{{ old('nama', $lomba->nama) }}">
                </div>

                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control" required value="{{ old('lokasi', $lomba->lokasi) }}">
                </div>

                @php
                    $tanggalValue = old('tanggal', \Carbon\Carbon::parse($lomba->tanggal)->format('Y-m-d\TH:i'));
                @endphp

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal & Waktu Lomba</label>
                    <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" required value="{{ $tanggalValue }}">
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control">{{ old('deskripsi', $lomba->deskripsi) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="status_lomba_id" class="form-label">Status Lomba</label>
                    <select name="status_lomba_id" id="status_lomba_id" class="form-control" required>
                        <option value="1" {{ old('status_lomba_id', $lomba->status_lomba_id) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="2" {{ old('status_lomba_id', $lomba->status_lomba_id) == 2 ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('manajemen-lomba.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
