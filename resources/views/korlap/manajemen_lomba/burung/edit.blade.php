@extends('layouts.app')

@section('title', 'Edit Lomba')

@section('content')
<div class="container mt-4">
    <div class="content-header px-2">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Edit Lomba</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                </ol>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="m-0"></h5>
            <div class="card-tools">
                <a href="{{ route('manajemen-lomba.kelola.burung.create', ['lomba_id' => $lomba_id]) }}" class="btn btn-tool"><i class="fas fa-arrow-alt-circle-left"></i></a>
            </div>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('manajemen-lomba.kelola.burung.update', ['lomba_id' => $lomba_id, 'burung_id' => $burung->id]) }}" method="post">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label>Jenis Burung</label>
                        <select name="jenis_burung_id" class="form-control @error('jenis_burung_id') is-invalid @enderror">
                            <option value="">-- Pilih Jenis Burung --</option>
                            @foreach ($jenisBurungs as $jenis)
                                <option value="{{ $jenis->id }}" @selected($jenis->id == $burung->jenis_burung_id)>
                                    {{ $jenis->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_burung_id')
                            <div class="invalid-feedback" role="alert">
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $kelasItem)
                                <option value="{{ $kelasItem->id }}" @selected($kelasItem->id == $burung->kelas_id)>
                                    {{ $kelasItem->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <div class="invalid-feedback" role="alert">
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-info btn-block btn-flat"><i class="fa fa-save"></i>
                        Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
