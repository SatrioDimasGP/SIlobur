@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('title', 'Hasil Lomba')

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Hasil Lomba</h4>
            </div>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0">Daftar Hasil Lomba</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('export.hasil_lomba') }}" class="btn btn-success mb-3">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <table id="datatable-main" class="table table-bordered table-striped text-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Gantangan</th>
                            <th>Pemilik</th>
                            <th>Jenis Burung</th>
                            <th>Total Poin</th>
                            <th>Status Juara</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penilaians as $index => $penilaian)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $penilaian->nomor_gantangan }}</td>
                                <td>{{ $penilaian->pemesan }}</td>
                                <td>{{ $penilaian->jenis }}</td>
                                <td>{{ $penilaian->total_poin }}</td>
                                <td>{{ $penilaian->status }}</td>
                                <td>
                                    <a href="{{ route('admin.hasil_lomba.show', [
                                        'nomor' => $penilaian->nomor_gantangan,
                                        'jenisBurungId' => $penilaian->jenis_burung_id,
                                        'kelasId' => $penilaian->kelas_id,
                                    ]) }}"
                                        class="btn btn-info btn-sm">Lihat</a>
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
                "responsive": true,
                "autoWidth": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "scrollX": true, // Enable horizontal scrolling for wide tables
                "pageLength": 10,
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
