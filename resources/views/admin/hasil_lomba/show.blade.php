@extends('layouts.app')

@section('title', 'Detail Hasil Lomba')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6 text-uppercase">
            <h4 class="m-0">Detail Hasil Nomor Gantangan: {{ $nomor }}</h4>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="m-0">Detail Penilaian</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nama Juri</th>
                            <th>Bendera</th>
                            <th>Point</th>
                            <th>Tahap</th>
                            <th>Waktu Dinilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penilaians as $penilaian)
                            <tr>
                                <td>{{ $penilaian->user->name ?? '-' }}</td>
                                <td>{{ ucfirst($penilaian->bendera->nama ?? '-') }}</td>
                                <td>{{ $penilaian->bendera->point ?? 0 }}</td>
                                <td>{{ ucfirst($penilaian->tahap->nama ?? '-') }}</td>
                                <td>{{ $penilaian->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <a href="{{ route('admin.hasil.index') }}" class="btn btn-secondary btn-sm mt-3">Kembali</a>
        </div>
    </div>
</div>
@endsection
