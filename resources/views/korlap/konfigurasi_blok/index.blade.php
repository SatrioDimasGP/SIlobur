@extends('layouts.app')

@section('title', 'Konfigurasi Blok & Gantangan')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 44px;
            padding: 6px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            overflow-y: auto;
            max-height: 150px;
            /* batasi tinggi agar scroll muncul jika banyak pilihan */
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin: 4px 2px 2px 0;
            padding: 4px 8px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 14px;
            border-radius: 3px;
        }

        /* Nonaktifkan tombol silang agar tidak bisa dihapus manual */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <h4 class="mb-4">Konfigurasi Blok & Gantangan</h4>

        {{-- Alert --}}
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

        @if (session('success') || session('error'))
            <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show"
                role="alert">
                {{ session('success') ?? session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            {{-- Kolom Form Blok --}}
            <div class="col-md-6">
                {{-- Form Tambah Blok --}}
                <div class="card mb-3">
                    <div class="card-header">Tambah Blok</div>
                    <div class="card-body">
                        <form action="{{ route('konfigurasi-blok.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Blok</label>
                                <input type="text" name="nama"
                                    class="form-control @error('nama') is-invalid @enderror" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="form-group mb-3">
                                    <label for="lomba">Lomba:</label>
                                    <select id="lomba-select" name="lomba_id" class="form-control" required>
                                        <option value="">-- Pilih Lomba --</option>
                                        @foreach ($lombas as $lomba)
                                            <option value="{{ $lomba->id }}">{{ $lomba->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('lomba_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="jenis_burung_kelas">Jenis Burung & Kelas:</label>
                                <select id="burung-kelas-select" name="burung_id" class="form-control" required>
                                    <option value="">-- Pilih Jenis Burung & Kelas --</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah Blok</button>
                        </form>
                    </div>
                </div>

                {{-- Tabel Daftar Blok --}}
                <div class="card">
                    <div class="card-header">Daftar Blok</div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped w-100" id="tabel-blok">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    {{-- <th>Lomba</th> --}}
                                    <th>Burung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bloks as $blok)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $blok->nama }}</td>
                                        {{-- <td>{{ $blok->lomba->nama ?? '-' }}</td> --}}
                                        <td>
                                            {{ optional(optional($blok->burung)->jenisBurung)->nama ?? '-' }} -
                                            {{ optional(optional($blok->burung)->kelas)->nama ?? '-' }}
                                        </td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-info dropdown-toggle"
                                                data-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('konfigurasi-blok.edit', $blok->id) }}">Edit</a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('konfigurasi-blok.destroy', $blok->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus blok ini?')">
                                                        @csrf @method('DELETE')
                                                        <button class="dropdown-item" type="submit">Hapus</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada blok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Kolom Form Tambah Gantangan ke Blok --}}
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Tambah Gantangan ke Blok</div>
                    <div class="card-body">
                        <form action="{{ route('konfigurasi-blok.gantangan.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="blok_id" class="form-label">Pilih Blok</label>
                                <select name="blok_id" class="form-select @error('blok_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Blok --</option>
                                    @foreach ($bloks as $blok)
                                        <option value="{{ $blok->id }}">
                                            {{ $blok->nama }} -
                                            {{ optional(optional($blok->burung)->jenisBurung)->nama ?? '?' }} -
                                            {{ optional(optional($blok->burung)->kelas)->nama ?? '?' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('blok_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="gantangan_id" class="form-label">Pilih Gantangan</label>
                                <select name="gantangan_id[]" id="gantangan_id"
                                    class="form-select select2-gantangan @error('gantangan_id') is-invalid @enderror"
                                    multiple required>
                                    @foreach ($gantangans as $gantangan)
                                        <option value="{{ $gantangan->id }}"> No {{ $gantangan->nomor }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Kamu bisa pilih lebih dari satu gantangan.</small>
                                @error('gantangan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah Nomor ke Blok</button>
                        </form>
                    </div>
                </div>

                {{-- Tabel Daftar Blok-Gantangan --}}
                <div class="card">
                    <div class="card-header">Daftar Blok Gantangan</div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped w-100" id="tabel-blok-gantangan">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Blok</th>
                                    <th>Gantangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blokGantangans as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->blok->nama }}</td>
                                        <td>No {{ $item->gantangan->nomor }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-info dropdown-toggle"
                                                    data-toggle="dropdown">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('konfigurasi-blok.gantangan.edit', $item->id) }}">Edit</a>
                                                    </li>
                                                    <li>
                                                        <form
                                                            action="{{ route('konfigurasi-blok.gantangan.destroy', ['blok_id' => $item->blok_id, 'id' => $item->id]) }}"
                                                            method="POST" onsubmit="return confirm('Hapus data ini?')">
                                                            @csrf @method('DELETE')
                                                            <button class="dropdown-item" type="submit">Hapus</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada relasi blok-gantangan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lombaSelect = document.getElementById('lomba-select');
            const burungKelasSelect = document.getElementById('burung-kelas-select');

            lombaSelect.addEventListener('change', function() {
                const lombaId = this.value;
                burungKelasSelect.innerHTML = '<option value="">-- Memuat data... --</option>';

                if (lombaId) {
                    fetch(`/penjadwalan-juri/get-jenis-burung-kelas/${lombaId}`)
                        .then(response => response.json())
                        .then(data => {
                            burungKelasSelect.innerHTML =
                                '<option value="">-- Pilih Jenis Burung & Kelas --</option>';
                            data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.id;
                                option.text = item.label;
                                burungKelasSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            burungKelasSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                            console.error(error);
                        });
                } else {
                    burungKelasSelect.innerHTML =
                        '<option value="">-- Pilih Jenis Burung & Kelas --</option>';
                }
            });
        });
    </script>
@endsection

@push('js')
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2-gantangan').select2({
                width: '100%',
                closeOnSelect: false
            });


            // Inisialisasi DataTables
            $('#tabel-blok, #tabel-blok-gantangan').DataTable({
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
