@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Penilaian Ajuan - Blok {{ $blok->nama }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('penilaian.store', ['lombaId' => $lomba->id,  'blokId' => $blok->id]) }}" method="POST" id="formPenilaian">
        @csrf

        <input type="hidden" name="blok_id" value="{{ $blok->id }}">
        <input type="hidden" name="jenis_burung_id" value="{{ request('jenis_burung_id') }}">
        <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
        <input type="hidden" name="lomba_id" value="{{ $lomba->id }}">

        <div class="row">
            @foreach ($blok->gantangans as $index => $item)
                <div class="col-md-4 mb-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body p-2">
                            <h5 class="card-title mb-3">Nomor: {{ $item->gantangan->nomor }}</h5>

                            <div class="d-flex justify-content-around">
                                <!-- Checkbox untuk memilih bendera -->
                                <label>
                                    <input type="checkbox" name="penilaian[{{ $item->id }}][bendera]" value="{{ $benderas->where('nama','putih')->first()->id }}" class="bendera-checkbox"> Putih
                                </label>
                                <label>
                                    <input type="checkbox" name="penilaian[{{ $item->id }}][bendera]" value="{{ $benderas->where('nama','hijau')->first()->id }}" class="bendera-checkbox"> Hijau
                                </label>
                                <label>
                                    <input type="checkbox" name="penilaian[{{ $item->id }}][bendera]" value="{{ $benderas->where('nama','hitam')->first()->id }}" class="bendera-checkbox"> Hitam
                                </label>
                            </div>

                            <!-- Input tersembunyi untuk menyimpan gantanganId -->
                            <input type="hidden" name="penilaian[{{ $item->id }}][gantanganId]" value="{{ $item->id }}">
                        </div>
                    </div>
                </div>

                @if(($index + 1) % 3 == 0)
                    <div class="w-100"></div>
                @endif
            @endforeach
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Submit Penilaian</button>
        </div>
    </form>
</div>
@endsection
