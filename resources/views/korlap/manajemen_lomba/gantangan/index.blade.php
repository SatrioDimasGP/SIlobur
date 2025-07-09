@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('title', 'Manajemen Gantangan')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 text-uppercase">Manajemen Gantangan</h4>

    {{-- Tombol kembali --}}
    <div class="mb-3 d-flex justify-content-end">
        <a href="{{ route('manajemen-lomba.kelola', $lomba_id) }}" class="btn btn-tool">
            <i class="fas fa-arrow-alt-circle-left"></i>
        </a>
    </div>

    <!-- Notifikasi -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Form Tambah Gantangan -->
    <div class="card mb-4">
        <div class="card-header">Tambah Gantangan</div>
        <div class="card-body">
            <form action="{{ route('manajemen-lomba.kelola.gantangan.store', $lomba_id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label for="nomor_awal" class="form-label">Nomor Awal</label>
                        <input type="number" name="nomor_awal" id="nomor_awal" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nomor_akhir" class="form-label">Nomor Akhir (Kosongkan untuk 1 nomor)</label>
                        <input type="number" name="nomor_akhir" id="nomor_akhir" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Gantangan -->
    <div class="card">
        <div class="card-header">Daftar Gantangan</div>
        <div class="card-body">
            <table id="datatable-main" class="table table-bordered table-striped text-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Gantangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gantangans as $index => $gantangan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $gantangan->nomor }}</td>
                        <td>
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-info dropdown-toggle" data-toggle="dropdown">
            <i class="fas fa-cog"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('manajemen-lomba.kelola.gantangan.edit', ['lomba_id' => $lomba_id, 'id' => $gantangan->id]) }}">Edit</a>
            </li>
            <li>
                <form action="{{ route('manajemen-lomba.kelola.gantangan.destroy', [$lomba_id, $gantangan->id]) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item">Hapus</button>
                </form>
            </li>
        </ul>
    </div>
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
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
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
            },
            "columnDefs": [
                {
                    targets: 1, // kolom Nomor Gantangan
                    type: 'num', // pastikan sorting numerik
                }
            ]
        });
    });
</script>
@endpush
