@extends('layouts.app')
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
@section('title', 'Riwayat Penilaian')
@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6 text-uppercase">
            <h4 class="m-0">Riwayat Penilaian</h4>
        </div>
    </div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="m-0"></h5>
        </div>
        <div class="card-body">
            <table id="datatable-main" class="table table-bordered table-striped text-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Blok</th>
                        <th>Nomor Gantangan</th>
                        <th>Bendera yang Diberikan</th>
                        <th>Poin yang Diberikan</th>
                        <th>Burung (Jenis - Kelas)</th>
                        <th>Status Tahap</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($riwayatAjuan as $index => $penilaian)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $penilaian->blokGantangan->blok->nama ?? '-' }}</td>
                            <td>{{ $penilaian->blokGantangan->gantangan->nomor ?? '-' }}</td>
                            <td>
                                @if ($penilaian->bendera)
                                    <span class="badge" style="background-color: {{ $penilaian->bendera->warna }}">
                                        {{ $penilaian->bendera->nama }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $penilaian->total_poin }}</td>
                            <td>
                                {{ optional($penilaian->burung->jenisBurung)->nama ?? '-' }} -
                                {{ optional($penilaian->burung->kelas)->nama ?? 'Tidak Diketahui' }}
                            </td>
                            <td>{{ $penilaian->tahap->nama ?? '-' }}</td>
                            <td>
                                <a href="{{ route('penilaian-riwayat.show', $penilaian->id) }}" class="btn btn-info btn-sm">Lihat</a>
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
            "pageLength": 10
        });
    });
</script>
@endpush
