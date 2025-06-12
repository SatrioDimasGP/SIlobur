@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Dashboard Admin</h2>

    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary">Penilaian Masuk</h5>
                    <p class="card-text fs-4 fw-bold">
                        {{ $totalPenilaianMasuk }}
                    </p>
                    <p class="text-muted">Jumlah total penilaian yang sudah dinilai</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
