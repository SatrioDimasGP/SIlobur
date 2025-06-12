@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="content-header px-2">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">{{ $gantangan->id ? 'Edit' : 'Tambah' }} Gantangan</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
                </div>
            </div>
        </div>

        <!-- Tampilkan pesan sukses atau error jika ada -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0">{{ $gantangan->id ? 'Edit' : 'Tambah' }} Gantangan</h5>
                <div class="card-tools">
                    <a href="{{ route('manajemen-lomba.kelola.gantangan.index', $lomba_id) }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ $gantangan->id
                    ? route('manajemen-lomba.kelola.gantangan.update', ['lomba_id' => $lomba_id, 'id' => $gantangan->id])
                    : route('manajemen-lomba.kelola.gantangan.store', ['lomba_id' => $lomba_id]) }}" method="POST">
                    @csrf
                    @if ($gantangan->id)
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label for="nomor">Nomor Gantangan</label>
                        <input type="number" name="nomor" class="form-control" id="nomor" value="{{ old('nomor', $gantangan->nomor) }}" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">{{ $gantangan->id ? 'Update' : 'Simpan' }} Gantangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
