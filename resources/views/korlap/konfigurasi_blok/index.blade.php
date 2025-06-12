@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Konfigurasi Blok & Gantangan</h4>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        {{-- Form Tambah Blok --}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Tambah Blok</div>
                <div class="card-body">
                    <form action="{{ route('konfigurasi-blok.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Blok</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" id="nama" required>
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="lomba_id" class="form-label">Pilih Lomba</label>
                            <select name="lomba_id" id="lomba_id" class="form-select @error('lomba_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Lomba --</option>
                                @foreach ($lombas as $lomba)
                                    <option value="{{ $lomba->id }}">{{ $lomba->nama }}</option>
                                @endforeach
                            </select>
                            @error('lomba_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Blok</button>
                    </form>
                </div>
            </div>

            {{-- Tabel Blok --}}
            <div class="card">
                <div class="card-header">Daftar Blok</div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Lomba</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bloks as $blok)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $blok->nama }}</td>
                                    <td>{{ $blok->lomba->nama ?? '-' }}</td>
                                    <td>
                                        <!-- Icon Dropdown for Actions -->
                                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('konfigurasi-blok.edit', $blok->id) }}">Edit</a>
                                            <form action="{{ route('konfigurasi-blok.destroy', $blok->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus blok ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Belum ada blok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form Tambah Gantangan ke Blok --}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Tambah Gantangan ke Blok</div>
                <div class="card-body">
                    <form action="{{ route('konfigurasi-blok.gantangan.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="blok_id" class="form-label">Pilih Blok</label>
                            <select name="blok_id" id="blok_id" class="form-select @error('blok_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Blok --</option>
                                @foreach ($bloks as $blok)
                                    <option value="{{ $blok->id }}">{{ $blok->nama }} - {{ $blok->lomba->nama ?? '-' }}</option>
                                @endforeach
                            </select>
                            @error('blok_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="gantangan_id" class="form-label">Pilih Gantangan</label>
                            <select name="gantangan_id" id="gantangan_id" class="form-select @error('gantangan_id') is-invalid @enderror" required style="max-height: 200px; overflow-y: auto;">
                                <option value="">-- Pilih Gantangan --</option>
                                @foreach ($gantangans as $gantangan)
                                    <option value="{{ $gantangan->id }}">
                                        No {{ $gantangan->nomor }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gantangan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Nomor ke Blok</button>
                    </form>
                </div>
            </div>

            {{-- Tabel Blok-Gantangan --}}
            <div class="card">
                <div class="card-header">Daftar Blok Gantangan</div>
                {{-- Form Pencarian Blok Gantangan --}}
<div class="mb-3">
    <form action="{{ route('konfigurasi-blok.index') }}" method="GET">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Cari Blok Gantangan..." value="{{ request()->search }}">
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>
</div>

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
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
                                        <!-- Icon Dropdown for Actions -->
                                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('konfigurasi-blok.gantangan.edit', ['id' => $item->id]) }}">Edit</a>
                                            <form action="{{ route('konfigurasi-blok.gantangan.destroy', ['blok_id' => $item->blok_id, 'id' => $item->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Belum ada relasi blok-gantangan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center">
                        {{ $blokGantangans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<!-- DataTables JS -->
<script src="{{ asset('') }}plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="{{ asset('') }}plugins/jszip/jszip.min.js"></script>
<script src="{{ asset('') }}plugins/pdfmake/pdfmake.min.js"></script>
<script src="{{ asset('') }}plugins/pdfmake/vfs_fonts.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
@endpush
