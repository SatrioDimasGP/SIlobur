@extends('layouts.app')

@section('title', 'Detail Hasil Lomba')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6 text-uppercase">
            <h4 class="m-0">
                Detail Hasil Nomor Gantangan: {{ $blokGantangan->gantangan->nomor }}
            </h4>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="m-0">
                Detail Penilaian Tahap: {{ $penilaians->first()->tahap->nama ?? '-' }}
            </h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Nama Lomba:</strong> {{ $blokGantangan->blok->lomba->nama ?? '-' }} <br>
                @php
                    $pemesananPertama = $blokGantangan->gantangan->pemesanan
                        ->where('burung_id', $burungId)
                        ->first();
                @endphp

                <strong>Jenis Burung:</strong> {{ $pemesananPertama->burung->jenisBurung->nama ?? '-' }} <br>
                <strong>Kelas:</strong> {{ $pemesananPertama->burung->kelas->nama ?? '-' }}
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nama Juri</th>
                            <th>Bendera</th>
                            <th>Poin</th>
                            <th>Waktu Dinilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penilaians as $penilaian)
                            <tr>
                                <td>{{ $penilaian->user->name ?? '-' }}</td>
                                <td>{{ $penilaian->bendera->nama ?? '-' }}</td>
                                <td>{{ $penilaian->bendera->point ?? 0 }}</td>
                                <td>{{ $penilaian->created_at ? $penilaian->created_at->format('d M Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada penilaian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Kembali</a>
        </div>
    </div>
</div>
@endsection
