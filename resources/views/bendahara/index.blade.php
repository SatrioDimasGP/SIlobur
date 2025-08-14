@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('title', 'Daftar Pemesanan')

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Daftar Pemesanan</h4>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0">Daftar Pemesanan</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('data-pendaftaran.export') }}" class="btn btn-success mb-3">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>

                <table id="datatable-main" class="table table-bordered table-striped text-sm">
                    <thead class="thead">
                        <tr>
                            <th>No</th>
                            <th>Nama Pemesan</th>
                            <th>Lomba</th>
                            <th>Jenis Burung</th>
                            <th>Kelas</th>
                            <th>No Gantangan</th>
                            <th>Harga</th> {{-- kolom baru --}}
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pemesanans as $index => $pemesanan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $pemesanan->user->name }}</td>
                                <td>{{ $pemesanan->lomba->nama }}</td>
                                <td>{{ $pemesanan->burung->jenisBurung->nama ?? '-' }}</td>
                                <td>{{ $pemesanan->burung->kelas->nama ?? '-' }}</td>
                                <td>{{ $pemesanan->gantangan->nomor ?? '-' }}</td>
                                <td>
                                    @if ($pemesanan->burung && $pemesanan->burung->kelas)
                                        Rp {{ number_format($pemesanan->burung->kelas->harga, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $pemesanan->status->nama }}</td>
                                <td>
                                    <a href="{{ route('data-pendaftaran.show', $pemesanan) }}"
                                        class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
