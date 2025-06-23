@extends('layouts.app')

@section('title', 'Detail Penilaian')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Penilaian</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('penilaian-riwayat.index') }}">Riwayat Penilaian</a></li>
                    <li class="breadcrumb-item active">Detail Penilaian</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0">Detail Penilaian</h5>
                        <div class="card-tools">
                            <a href="{{ route('penilaian-riwayat.index') }}" class="btn btn-tool">
                                <i class="fas fa-arrow-alt-circle-left"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Blok</th>
                                        <th>Nomor Gantangan</th>
                                        <th>Bendera yang Diberikan</th>
                                        <th>Poin yang Diberikan</th>
                                        <th>Burung (Jenis - Kelas)</th>
                                        <th>Status Tahap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $penilaian->blokGantangan->blok->nama ?? '-' }}</td>
                                        <td>{{ $penilaian->blokGantangan->gantangan->nomor ?? '-' }}</td>
                                        <td>
                                            @if ($penilaian->bendera)
                                                <span class="badge" style="background-color: {{ $penilaian->bendera->warna }}">
                                                    {{ $penilaian->bendera->nama }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $penilaian->total_poin }}</td>
                                        <td>
                                            {{ optional($penilaian->burung->jenisBurung)->nama ?? '-' }} -
                                            {{ optional($penilaian->burung->kelas)->nama ?? 'Tidak Diketahui' }}
                                        </td>
                                        <td>{{ $penilaian->tahap->nama ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
