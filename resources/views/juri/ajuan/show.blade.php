@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Penilaian Ajuan - Blok {{ $blok->nama }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('penilaian.store', ['lombaId' => $lomba->id, 'blokId' => $blok->id]) }}" method="POST"
            id="formPenilaian">
            @csrf

            <input type="hidden" name="blok_id" value="{{ $blok->id }}">
            <input type="hidden" name="jenis_burung_id" value="{{ request('jenis_burung_id') }}">
            <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
            <input type="hidden" name="lomba_id" value="{{ $lomba->id }}">

            <div class="row">
                @foreach ($blok->gantangans->sortBy('gantangan.nomor') as $index => $item)
                    <div class="col-md-4 mb-4">
                        <div
                            class="card text-center shadow-sm {{ in_array($item->id, $blokGantanganTerblokir) ? 'bg-light text-muted border-secondary' : '' }}">
                            <div class="card-body p-2">
                                <h5 class="card-title mb-3">
                                    Nomor: {{ $item->gantangan->nomor }}
                                </h5>

                                @php
                                    $name = "penilaian[{$item->id}][bendera]";
                                    $benderaPutih = $benderas->where('nama', 'putih')->first();
                                    $benderaHijau = $benderas->where('nama', 'hijau')->first();
                                    $benderaHitam = $benderas->where('nama', 'hitam')->first();
                                    $terblokir = in_array($item->id, $blokGantanganTerblokir);
                                @endphp

                                @if ($terblokir)
                                    <p class="text-danger mt-3 mb-0">Sudah diberi bendera hitam oleh juri lain</p>
                                @else
                                    <div class="d-flex justify-content-around">
                                        <label class="badge bg-success">
                                            <input type="radio" name="{{ $name }}"
                                                value="{{ $benderaHijau->id }}">
                                            Hijau
                                        </label>
                                        <label class="badge"
                                            style="background-color: #ffffff; color: #000; border: 1px solid #ddd;">
                                            <input type="radio" name="{{ $name }}"
                                                value="{{ $benderaPutih->id }}">
                                            Putih
                                        </label>
                                        <label class="badge bg-dark text-white">
                                            <input type="radio" name="{{ $name }}"
                                                value="{{ $benderaHitam->id }}">
                                            Hitam
                                        </label>
                                    </div>
                                @endif

                                <input type="hidden" name="penilaian[{{ $item->id }}][gantanganId]"
                                    value="{{ $item->id }}">
                            </div>
                        </div>

                    </div>

                    @if (($index + 1) % 3 == 0)
                        <div class="w-100"></div>
                    @endif
                @endforeach
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Submit Penilaian</button>
            </div>
        </form>
        <script>
            // Aktifkan fitur klik ulang untuk uncheck radio di form Ajuan
            document.querySelectorAll('#formPenilaian input[type="radio"]').forEach(radio => {
                radio.addEventListener('mousedown', function() {
                    // Simpan apakah sudah ter-check sebelum klik
                    this.wasChecked = this.checked;
                });

                radio.addEventListener('click', function() {
                    // Jika sebelumnya sudah ter-check, maka hilangkan
                    if (this.wasChecked) {
                        this.checked = false;
                        this.dispatchEvent(new Event('change'));
                    }
                });
            });
        </script>
    </div>
@endsection
