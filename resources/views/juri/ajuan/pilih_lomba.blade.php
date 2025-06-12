@extends('layouts.app')

@section('title', 'Pilih Lomba')

@section('content')
<div class="container py-4">
    <h1 class="text-2xl font-bold mb-6">Pilih Lomba</h1>

    @if($lombas->isEmpty())
        <div class="alert alert-info">
            Tidak ada lomba yang tersedia saat ini.
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
                            <a href="{{ route('penilaian-ajuan.index', $lomba->id) }}" class="btn btn-primary">
                                Pilih Lomba
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
