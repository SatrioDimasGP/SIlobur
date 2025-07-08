@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Selamat datang {{ ucwords(auth()->user()->name) }}</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
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
                            <h5 class="m-0">Dashboard Peserta</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @forelse ($lombaTerbuka as $lomba)
                                    <div class="col-md-6">
                                        <div class="card card-success mb-3">
                                            <div class="card-header">
                                                <h5 class="m-0">{{ $lomba->nama }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($lomba->tanggal)->format('d M Y') }}</p>
                                                <p><strong>Lokasi:</strong> {{ $lomba->lokasi }}</p>
                                                <p><strong>Deskripsi:</strong> {{ $lomba->deskripsi }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p>Tidak ada lomba yang sedang dibuka.</p>
                                    </div>
                                @endforelse
                            </div> <!-- /.row -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $('.toast').toast('show')
    </script>
@endpush
