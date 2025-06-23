@extends('layouts.app')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="container">
        <h1 class="mb-4">Penugasan Juri</h1>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <!-- Form Tambah Penugasan -->
        <form method="POST" action="{{ route('penjadwalan-juri.store') }}">
            @csrf
            <div class="card mb-4">
                <div class="card-header">Penugasan Juri Baru</div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="lomba">Lomba:</label>
                        <select id="lomba-select" name="lomba_id" class="form-control" required>
                            <option value="">-- Pilih Lomba --</option>
                            @foreach ($lombas as $lomba)
                                <option value="{{ $lomba->id }}">{{ $lomba->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="jenis_burung_kelas">Jenis Burung & Kelas:</label>
                        <select id="burung-kelas-select" name="burung_id" class="form-control" required>
                            <option value="">-- Pilih Jenis Burung & Kelas --</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="juri">Juri:</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">-- Pilih Juri --</option>
                            @foreach ($juris as $juri)
                                <option value="{{ $juri->id }}">{{ $juri->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">Simpan</button>
                </div>
            </div>
        </form>

        <!-- Tabel Penugasan -->
        <h5 class="mb-3">Daftar Penugasan Juri</h5>
        <div class="card card-primary card-outline">
            <div class="card-body">
                <table id="datatable-main" class="table table-bordered table-striped text-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Lomba</th>
                            <th>Juri</th>
                            <th>Jenis Burung & Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penugasanJuri as $index => $penugasan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $penugasan->lomba->nama }}</td>
                                <td>{{ $penugasan->user->name }}</td>
                                <td>
                                    {{ $penugasan->blok->burung->jenisBurung->nama ?? '-' }} -
                                    {{ $penugasan->blok->burung->kelas->nama ?? '-' }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-info dropdown-toggle" data-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('penjadwalan-juri.edit', $penugasan->id) }}">
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('penjadwalan-juri.destroy', $penugasan->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus penugasan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item">Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data penugasan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JS Konfirmasi -->
    <script>
        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus penugasan ini?");
        }
    </script>

    <!-- JS Dropdown Dinamis -->
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

@push('scripts')
    <!-- jQuery & DataTables -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(function() {
            $('#datatable-main').DataTable({
                responsive: true,
                autoWidth: false,
                lengthChange: true,
                ordering: true,
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)"
                },
                columnDefs: [{
                    orderable: false,
                    targets: [4]
                }]
            });
        });
    </script>
@endpush
