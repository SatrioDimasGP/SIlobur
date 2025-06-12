@extends('layouts.app')

@section('title', 'Lomba & Jadwal')

@section('content')
<div class="container">
    <h1 class="mb-4">Lomba yang Sedang Dibuka</h1>

    @if($lombas->isEmpty())
        <div class="alert alert-info">
            Tidak ada lomba yang sedang dibuka saat ini.
        </div>
    @else
        <div class="row">
            @foreach($lombas as $lomba)
                <div class="col-md-6">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $lomba->nama }}</h5>
                            <p class="card-text">
                                Tanggal: {{ \Carbon\Carbon::parse($lomba->tanggal)->translatedFormat('l, d F Y') }}<br>
                                Lokasi: {{ $lomba->lokasi }}
                            </p>
                            <a href="{{ route('lomba.daftar', $lomba->id) }}" class="btn btn-primary">
                                Daftar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
