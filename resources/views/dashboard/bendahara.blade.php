@extends('layouts.app')

@section('title', 'Dashboard Bendahara')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Dashboard Bendahara</h2>

    <div class="row">
        <!-- Total peserta -->
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title text-success">Total Peserta Terdaftar</h5>
                    <p class="card-text fs-4 fw-bold">{{ $totalPeserta }}</p>
                    <p class="text-muted">Jumlah semua pendaftar lomba</p>
                </div>
            </div>
        </div>

        <!-- Peserta hari ini -->
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary">Pendaftaran Hari Ini</h5>
                    <p class="card-text fs-4 fw-bold">{{ $pesertaHariIni }}</p>
                    <p class="text-muted">Jumlah pendaftar yang masuk hari ini</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
