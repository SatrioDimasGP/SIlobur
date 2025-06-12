@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h4>Edit Blok Gantangan</h4>
        <div class="card-tools">
            <a href="{{ route('konfigurasi-blok.index') }}" class="btn btn-tool">
                <i class="fas fa-arrow-alt-circle-left"></i>
            </a>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form Edit Blok Gantangan --}}
    <div class="card">
        <div class="card-body">
            <form action="{{ route('konfigurasi-blok.gantangan.update', ['blok_id' => $blokGantangan->blok_id, 'id' => $blokGantangan->id]) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Pilih Blok --}}
                <div class="mb-3">
                    <label for="blok_id" class="form-label">Pilih Blok</label>
                    <select name="blok_id" id="blok_id" class="form-select @error('blok_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Blok --</option>
                        @foreach ($bloks as $blok)
                            <option value="{{ $blok->id }}" {{ $blok->id == $blokGantangan->blok_id ? 'selected' : '' }}>
                                {{ $blok->nama }} - {{ $blok->lomba->nama ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('blok_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Pilih Gantangan --}}
                <div class="mb-3">
                    <label for="gantangan_id" class="form-label">Pilih Gantangan</label>
                    <select name="gantangan_id" id="gantangan_id" class="form-select @error('gantangan_id') is-invalid @enderror" required style="max-height: 200px; overflow-y: auto;">
                        <option value="">-- Pilih Gantangan --</option>
                        @foreach ($gantangans as $gantangan)
                            <option value="{{ $gantangan->id }}" {{ $gantangan->id == $blokGantangan->gantangan_id ? 'selected' : '' }}>
                                No {{ $gantangan->nomor }}
                            </option>
                        @endforeach
                    </select>
                    @error('gantangan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
