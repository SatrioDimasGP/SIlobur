@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">DASHBOARD SUPERADMIN</h1>
                <p>Selamat datang di Silobur</p>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">

            {{-- Ringkasan Data --}}
            <div class="col-md-3">
                <div class="card bg-primary text-white mb-4 shadow">
                    <div class="card-body">
                        <h5>Total User</h5>
                        <h2>{{ $totalUser }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white mb-4 shadow">
                    <div class="card-body">
                        <h5>Total Juri</h5>
                        <h2>{{ $totalJuri }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark mb-4 shadow">
                    <div class="card-body">
                        <h5>Lomba Aktif</h5>
                        <h2>{{ $totalLombaAktif }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white mb-4 shadow">
                    <div class="card-body">
                        <h5>Pendaftaran Hari Ini</h5>
                        <h2>{{ $pesertaHariIni }}</h2>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
