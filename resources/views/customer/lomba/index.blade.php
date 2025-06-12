@extends('layouts.app')

@section('title', 'Lomba Saya')

@section('content')
<div class="container">
    <h1 class="mb-4">Lomba yang Sudah Anda Ikuti</h1>

    @if($pemesanans->isEmpty())
        <div class="alert alert-info">Anda belum mendaftar lomba apapun.</div>
    @else
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nama Lomba</th>
                    <th>Tanggal</th>
                    <th>Burung (Jenis - Kelas)</th>
                    <th>No. Gantangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemesanans as $createdAt => $group)
                    @php
                        $first = $group->first();
                        $groupedBurung = $group->groupBy(function($item) {
                            return optional($item->burung->jenisBurung)->nama . ' - ' . optional($item->burung->kelas)->nama;
                        });
                    @endphp
                    <tr>
                        <td>{{ $first->lomba->nama }}</td>
                        <td>{{ \Carbon\Carbon::parse($first->lomba->tanggal)->format('d M Y') }}</td>
                        <td>
                            <ul class="mb-0">
                                @foreach($groupedBurung as $jenisKelas => $items)
                                    <li>{{ $jenisKelas }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="mb-0">
                                @foreach($groupedBurung as $jenisKelas => $items)
                                    <li>
                                        {{ $items->pluck('gantangan.nomor')->filter()->map(fn($n) => 'No ' . $n)->implode(', ') }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <span class="badge {{ optional($first->status)->nama === 'Lunas' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst(optional($first->status)->nama ?? '-') }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('pemesanans.show', ['created_at' => $createdAt]) }}" class="btn btn-sm btn-primary">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@if(session('download_pdf') && session('transaksi_id'))
    <script>
        window.onload = function () {
            window.location.href = "{{ route('pemesanans.bukti-pembayaran', session('transaksi_id')) }}";
        };
    </script>
@endif

@endsection
