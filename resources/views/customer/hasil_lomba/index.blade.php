@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('title', 'Hasil Lomba Saya')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6 text-uppercase">
            <h4 class="m-0">Hasil Lomba Saya</h4>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-body">
            <table id="datatable-main" class="table table-bordered table-striped text-sm">
<thead>
<tr>
    <th>No</th>
    <th>Nama Lomba</th>
    <th>Jenis - Kelas</th>
    <th>No. Gantangan</th>
    <th>Tahap</th>
    <th>Total</th>
    <th>Status Juara</th>
    <th>Aksi</th> <!-- Menambahkan kolom Aksi -->
</tr>
</thead>
<tbody>
@foreach($penilaians as $index => $p)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $p->nama_lomba }}</td>
    <td>{{ $p->jenis }} - {{ $p->kelas }}</td>
    <td>{{ $p->nomor_gantangan }}</td>
    <td>{{ $p->tahap }}</td>
    <td>{{ $p->total }}</td>
    <td>{{ $p->status_juara }}</td>
    <td>
        <!-- Tombol untuk melihat detail lomba -->
        <a href="{{ route('hasil_lomba.show', ['blokGantanganId' => $p->blok_gantangan_id, 'burungId' => $p->burung_id, 'tahapId' => $p->tahap_id]) }}" class="btn btn-info btn-sm">Lihat Detail</a>

        {{-- <a href="{{ route('hasil_lomba.show', ['id' => $p->blok_gantangan_id, 'tahapId' => $p->tahap_id]) }}" class="btn btn-info btn-sm">Lihat Detail</a> --}}
        {{-- <a href="{{ route('hasil_lomba.show', ['id' => $p->blok_id, 'tahapId' => $p->tahap_id]) }}" class="btn btn-info btn-sm">Lihat Detail</a> --}}
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
    $(document).ready(function () {
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
