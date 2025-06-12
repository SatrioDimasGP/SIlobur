@extends('layouts.app')

@section('title', 'Detail Penilaian')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6 text-uppercase">
            <h4 class="m-0">Detail Penilaian</h4>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.monitor.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-body">
            <dl class="row text-sm">
                <dt class="col-sm-3">Nama Juri</dt>
                <dd class="col-sm-9">{{ $penilaian->user->name ?? '-' }}</dd>

                <dt class="col-sm-3">Blok</dt>
                <dd class="col-sm-9">{{ $penilaian->blokGantangan->blok->nama ?? '-' }}</dd>

                <dt class="col-sm-3">Nomor Gantangan</dt>
                <dd class="col-sm-9">{{ $penilaian->blokGantangan->gantangan->nomor ?? '-' }}</dd>

                <dt class="col-sm-3">Bendera Diberikan</dt>
                <dd class="col-sm-9">
                     @if ($penilaian->bendera)
                                    <span class="badge" style="background-color: {{ $penilaian->bendera->warna }}">
                                        {{ $penilaian->bendera->nama }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                </dd>

                <dt class="col-sm-3">Poin</dt>
                <dd class="col-sm-9">{{ $penilaian->bendera->point ?? 0 }}</dd>

                <dt class="col-sm-3">Jenis Burung</dt>
                <dd class="col-sm-9">{{ $penilaian->burung->jenisBurung->nama ?? '-' }}</dd>
                            
                <dt class="col-sm-3">Kelas</dt>
                <dd class="col-sm-9">{{ $penilaian->burung->kelas->nama ?? '-' }}</dd>

                <dt class="col-sm-3">Tahap Penilaian</dt>
                <dd class="col-sm-9">{{ $penilaian->tahap->nama ?? '-' }}</dd>

                <dt class="col-sm-3">Waktu Penilaian</dt>
                <dd class="col-sm-9">{{ $penilaian->created_at ? $penilaian->created_at->format('d-m-Y H:i:s') : '-' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
