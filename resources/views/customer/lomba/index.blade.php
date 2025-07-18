@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('title', 'Lomba Saya')

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Lomba yang Sudah Anda Ikuti</h4>
            </div>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-body">
                @if ($pemesanans->isEmpty())
                    <div class="alert alert-info">Anda belum mendaftar lomba apapun.</div>
                @else
                    <table id="datatable-main" class="table table-bordered table-striped text-sm">
                        <thead>
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
                            @foreach ($pemesanans as $createdAt => $group)
                                @php
                                    $first = $group->first();
                                    $groupedBurung = $group->groupBy(function ($item) {
                                        return optional($item->burung->jenisBurung)->nama .
                                            ' - ' .
                                            optional($item->burung->kelas)->nama;
                                    });
                                @endphp
                                <tr>
                                    <td>{{ $first->lomba->nama }}</td>
                                    <td>{{ \Carbon\Carbon::parse($first->lomba->tanggal)->format('d M Y') }}</td>
                                    <td>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($groupedBurung as $jenisKelas => $items)
                                                <li>{{ $jenisKelas }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($groupedBurung as $jenisKelas => $items)
                                                <li>{{ $items->pluck('gantangan.nomor')->filter()->map(fn($n) => 'No ' . $n)->implode(', ') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @php
                                            $statusNama = strtolower(trim(optional($first->status)->nama));
                                            $badgeClass =
                                                $statusNama === 'lunas' ? 'bg-success' : 'bg-warning text-dark';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($statusNama ?: '-') }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('pemesanans.show', ['created_at' => $createdAt]) }}"
                                            class="btn btn-sm btn-primary">Lihat Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    @if (session('download_pdf') && session('transaksi_id'))
        <script>
            window.onload = function() {
                window.location.href = "{{ route('pemesanans.bukti-pembayaran', session('transaksi_id')) }}";
            };
        </script>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('') }}plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#datatable-main').DataTable({
                "pageLength": 10,
                "responsive": true,
                "autoWidth": false,
                "ordering": true,
                "lengthChange": true,
                "searching": true,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(difilter dari _MAX_ total data)"
                }
            });
        });
    </script>
@endpush
