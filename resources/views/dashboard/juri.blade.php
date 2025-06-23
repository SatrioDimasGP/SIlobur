@extends('layouts.app')

@section('title', 'Dashboard Juri')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Selamat Datang, Juri!</h1>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Informasi Tugas Hari Ini
                </div>
                <div class="card-body">
                    <p>Berikut adalah penugasan Anda untuk hari ini. Silakan cek jadwal dan mulai penilaian sesuai blok yang telah ditentukan.</p>
                    <!-- Tambahkan tabel/blok penilaian di sini jika ada -->
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    Riwayat Penilaian
                </div>
                <div class="card-body">
                    <p>Lihat riwayat penilaian Anda pada lomba-lomba sebelumnya.</p>
                    <a href="{{ route('penilaian-riwayat.show', auth()->id()) }}" class="btn btn-success">Lihat Riwayat</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
