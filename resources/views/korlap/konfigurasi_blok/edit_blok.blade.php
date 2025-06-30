@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h4>Edit Blok</h4>
            <div class="card-tools">
                <a href="{{ route('konfigurasi-blok.index') }}" class="btn btn-tool">
                    <i class="fas fa-arrow-alt-circle-left"></i>
                </a>
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Form Edit Blok --}}
        <div class="card">
            <div class="card-body">
                <form action="{{ route('konfigurasi-blok.update', $blok->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Blok</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                            id="nama" value="{{ old('nama', $blok->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lomba_id" class="form-label">Pilih Lomba</label>
                        <select name="lomba_id" id="lomba_id" class="form-select @error('lomba_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Lomba --</option>
                            @foreach ($lombas as $lomba)
                                <option value="{{ $lomba->id }}" {{ $lomba->id == $blok->lomba_id ? 'selected' : '' }}>
                                    {{ $lomba->nama }}</option>
                            @endforeach
                        </select>
                        @error('lomba_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="burung_id" class="form-label">Jenis Burung & Kelas</label>
                        <select name="burung_id" id="burung_id" class="form-select @error('burung_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Jenis Burung & Kelas --</option>
                            @foreach ($jenisBurungKelas as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $blok->burung_id ? 'selected' : '' }}>
                                    {{ $item->jenisBurung->nama }} - {{ $item->kelas->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('burung_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
