@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ isset($penugasanJuri) ? 'Edit' : 'Tambah' }} Penugasan Juri</h1>
    {{-- <form action="{{ isset($penugasanJuri) ? route('penugasan-juri.update', $penugasanJuri->id) : route('penugasan-juri.store') }}" method="POST"> --}}

        <form action="{{ isset($penugasanJuri) ? route('penjadwalan-juri.update', $penugasanJuri->id) : route('penjadwalan-juri.store') }}" method="POST">
        @csrf
        @if(isset($penugasanJuri))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label for="lomba_id" class="form-label">Lomba</label>
            <select id="lomba_id" name="lomba_id" class="form-control" required>
                <option value="">Pilih Lomba</option>
                @foreach($lombas as $lomba)
                    <option value="{{ $lomba->id }}" {{ isset($penugasanJuri) && $penugasanJuri->lomba_id == $lomba->id ? 'selected' : '' }}>{{ $lomba->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="user_id" class="form-label">Juri</label>
            <select id="user_id" name="user_id" class="form-control" required>
                <option value="">Pilih Juri</option>
                {{-- @foreach($juri as $jur) --}}
                @foreach($juris as $jur)
                    <option value="{{ $jur->id }}" {{ isset($penugasanJuri) && $penugasanJuri->user_id == $jur->id ? 'selected' : '' }}>{{ $jur->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="blok_id" class="form-label">Blok</label>
            <select id="blok_id" name="blok_id" class="form-control" required>
                <option value="">Pilih Blok</option>
                @foreach($bloks as $blok)
                <option value="{{ $blok->id }}" {{ isset($penugasanJuri) && $penugasanJuri->blok_id == $blok->id ? 'selected' : '' }}>{{ $blok->nama }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">{{ isset($penugasanJuri) ? 'Update' : 'Simpan' }}</button>
        <a href="{{ route('penjadwalan-juri.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
