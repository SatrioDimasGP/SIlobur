@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header & Back Button --}}
    <div class="d-flex justify-content-between mb-4">
        <h4>{{ isset($penugasanJuri) ? 'Edit' : 'Tambah' }} Penugasan Juri</h4>
        <div class="card-tools">
            <a href="{{ route('penjadwalan-juri.index') }}" class="btn btn-tool" title="Kembali">
                <i class="fas fa-arrow-alt-circle-left fa-lg"></i>
            </a>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form Card --}}
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($penugasanJuri) ? route('penjadwalan-juri.update', $penugasanJuri->id) : route('penjadwalan-juri.store') }}" method="POST">
                @csrf
                @if(isset($penugasanJuri))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="lomba_id" class="form-label">Lomba</label>
                    <select id="lomba_id" name="lomba_id" class="form-select @error('lomba_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Lomba --</option>
                        @foreach($lombas as $lomba)
                            <option value="{{ $lomba->id }}" {{ old('lomba_id', $penugasanJuri->lomba_id ?? '') == $lomba->id ? 'selected' : '' }}>{{ $lomba->nama }}</option>
                        @endforeach
                    </select>
                    @error('lomba_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="user_id" class="form-label">Juri</label>
                    <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Juri --</option>
                        @foreach($juris as $jur)
                            <option value="{{ $jur->id }}" {{ old('user_id', $penugasanJuri->user_id ?? '') == $jur->id ? 'selected' : '' }}>{{ $jur->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="blok_id" class="form-label">Blok</label>
                    <select id="blok_id" name="blok_id" class="form-select @error('blok_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Blok --</option>
                        @foreach($bloks as $blok)
                            <option value="{{ $blok->id }}" {{ old('blok_id', $penugasanJuri->blok_id ?? '') == $blok->id ? 'selected' : '' }}>{{ $blok->nama }}</option>
                        @endforeach
                    </select>
                    @error('blok_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-success">{{ isset($penugasanJuri) ? 'Update' : 'Simpan' }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
