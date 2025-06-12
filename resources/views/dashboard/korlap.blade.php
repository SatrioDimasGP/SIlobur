@extends('layouts.app')

@section('title', 'Dashboard Korlap')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Dashboard Korlap</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Daftar Lomba Keseluruhan</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Lomba</th>
                            <th>Status</th>
                            <th>Jumlah Blok</th>
                            <th>Jumlah Juri Ditugaskan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lombas as $index => $lomba)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $lomba->nama }}</td>
                                <td>{{ $lomba->status }}</td>
                                <td>{{ $lomba->total_blok }}</td>
                                <td>{{ $lomba->total_juri }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data lomba.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
