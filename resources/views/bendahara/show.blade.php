@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
<div class="container mt-4">
    <div class="content-header px-2">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Pemesanan</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                </ol>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="m-0">Informasi Pemesanan</h5>
            <div class="card-tools">
                <a href="{{ route('data-pendaftaran.index') }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
            </div>
        </div>

        <div class="card-body">
            <p><strong>Nama Pemesan:</strong> {{ $pemesanan->user->name }}</p>
            <p><strong>Lomba:</strong> {{ $pemesanan->lomba->nama }}</p>
            <p><strong>Jenis Burung:</strong> {{ $pemesanan->burung->jenisBurung->nama ?? '-' }}</p>
            <p><strong>Kelas:</strong> {{ $pemesanan->burung->kelas->nama ?? '-' }}</p>
            <p><strong>Gantangan:</strong> {{ $pemesanan->gantangan->nomor ?? '-' }}</p>
            <p><strong>Status Pembayaran:</strong> {{ $pemesanan->status->nama }}</p>
            <p><strong>Tanggal Pemesanan:</strong> {{ $pemesanan->created_at->format('d-m-Y H:i') }}</p>
        </div>
    </div>

    <!--<div class="d-flex justify-content-between mt-3">-->
    <!--    <a href="{{ route('data-pendaftaran.update-status', $pemesanan) }}" class="btn btn-primary">Perbarui Status Pembayaran</a>-->
    <!--</div>-->
</div>
@endsection
