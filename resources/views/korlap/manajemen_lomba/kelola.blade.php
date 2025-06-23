@extends('layouts.app')

@section('title', 'Kelola Lomba: ' . $lomba->nama)

@push('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="container">
    <h1 class="mb-4">Kelola Lomba: {{ $lomba->nama }}</h1>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('manajemen-lomba.kelola.burung.create', ['lomba_id' => $lomba->id]) }}" class="btn btn-success">
            + Tambah Gabungan Jenis Burung dan Kelas
        </a>
        <a href="{{ route('manajemen-lomba.kelola.gantangan.index', ['lomba_id' => $lomba->id]) }}" class="btn btn-warning">
            Kelola Gantangan
        </a>
        <a href="{{ route('manajemen-lomba.index') }}" class="btn btn-tool">
            <i class="fas fa-arrow-alt-circle-left"></i>
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Form Jenis Burung --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Tambah Jenis Burung</div>
                <div class="card-body">
                    <form action="{{ route('manajemen-lomba.kelola.jenis-burung.store', ['lomba_id' => $lomba->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="lomba_id" value="{{ $lomba->id }}">
                        <div class="form-group mb-2">
                            <label for="jenis_burung">Nama Jenis Burung</label>
                            <input type="text" name="jenis_burung" id="jenis_burung" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>

            {{-- Tabel Jenis Burung --}}
           <div class="card">
    <div class="card-header">Daftar Jenis Burung</div>
    <div class="card-body">
        <table class="table table-bordered table-striped w-100" id="tabel-jenis-burung">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jenisBurungs as $jenisBurung)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $jenisBurung->nama }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-info dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('manajemen-lomba.kelola.burung.jenis-burung.edit', ['lomba_id' => $lomba->id, 'id' => $jenisBurung->id]) }}">Edit</a>
                                    </li>
                                    <li>
                                        <form action="{{ route('manajemen-lomba.kelola.jenis-burung.destroy', ['lomba_id' => $lomba->id, 'id' => $jenisBurung->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item">Hapus</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center">Belum ada jenis burung.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
        </div>

        {{-- Form Kelas --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Tambah Kelas</div>
                <div class="card-body">
                    <form action="{{ route('manajemen-lomba.kelola.kelas.store', ['lomba_id' => $lomba->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="lomba_id" value="{{ $lomba->id }}">
                        <div class="form-group mb-2">
                            <label for="kelas">Nama Kelas</label>
                            <input type="text" name="kelas" id="kelas" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="harga">Harga</label>
                            <input type="text" name="harga" id="harga" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>

            {{-- Tabel Kelas --}}
            <div class="card">
    <div class="card-header">Daftar Kelas</div>
    <div class="card-body">
        <table class="table table-bordered table-striped w-100" id="tabel-kelas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-info dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('manajemen-lomba.kelola.kelas.edit', ['lomba_id' => $lomba->id, 'id' => $item->id]) }}">Edit</a>
                                    </li>
                                    <li>
                                        <form action="{{ route('manajemen-lomba.kelola.kelas.destroy', ['lomba_id' => $lomba->id, 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item">Hapus</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Belum ada kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hargaInput = document.getElementById('harga');

        hargaInput.addEventListener('input', function (e) {
            let value = this.value.replace(/\./g, ''); // Hilangkan titik jika ada
            if (!isNaN(value)) {
                this.value = Number(value).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
        });

        // Saat submit, hilangkan titik supaya disimpan sebagai angka murni
        hargaInput.closest('form').addEventListener('submit', function () {
            hargaInput.value = hargaInput.value.replace(/\./g, '');
        });
    });
</script>
@endsection
@push('js')
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#tabel-jenis-burung, #tabel-kelas').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ entri',
                zeroRecords: 'Tidak ada data ditemukan',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                infoEmpty: 'Tidak ada entri tersedia',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
        });
    });
</script>
@endpush

