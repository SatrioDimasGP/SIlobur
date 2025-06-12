@extends('layouts.app')

@section('title', 'Daftar Pemesanan')

@section('content')
<div class="container">
    <h1 class="my-4">Daftar Pemesanan</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('data-pendaftaran.index') }}" method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Cari..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Nama Pemesan</th>
                <th>Lomba</th>
                <th>Jenis Burung</th>
                <th>Kelas</th>
                <th>No Gantangan</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemesanans as $pemesanan)
                <tr>
                    <td>{{ ($pemesanans->currentPage() - 1) * $pemesanans->perPage() + $loop->iteration }}</td>
                    <td>{{ $pemesanan->user->name }}</td>
                    <td>{{ $pemesanan->lomba->nama }}</td>
                    {{-- Ganti akses hargaBurung ke burung langsung --}}
                    <td>{{ $pemesanan->burung->jenisBurung->nama ?? '-' }}</td>
                    <td>{{ $pemesanan->burung->kelas->nama ?? '-' }}</td>
                    <td>{{ $pemesanan->gantangan->nomor ?? '-' }}</td>
                    <td>{{ $pemesanan->status->nama }}</td>
                    <td>
                        <a href="{{ route('data-pendaftaran.show', $pemesanan) }}" class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        {{ $pemesanans->withQueryString()->links() }}
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (window.performance && performance.navigation.type === performance.navigation.TYPE_RELOAD) {
            const url = new URL(window.location.href);
            if (url.searchParams.has('search')) {
                url.searchParams.delete('search');
                window.location.href = url.toString();
            }
        }
    });
</script>
@endsection
