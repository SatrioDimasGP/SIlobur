@extends('layouts.app')

@section('title', 'Manajemen Lomba')

@section('content')
<div class="container">
    <h1 class="mb-4">Manajemen Lomba</h1>

    <a href="{{ route('manajemen-lomba.create') }}" class="btn btn-primary mb-3">Tambah Lomba</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Lomba</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lombas as $lomba)
                <tr>
                    <td>{{ $lomba->nama }}</td>
                    <td>{{ $lomba->lokasi }}</td>
                    <td>{{ \Carbon\Carbon::parse($lomba->tanggal)->translatedFormat('d F Y') }}</td>
                    <td>{{ $lomba->statusLomba->nama }}</td>
                    <td>
                        <a href="{{ route('manajemen-lomba.edit', $lomba->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('manajemen-lomba.destroy', $lomba->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                        <a href="{{ route('manajemen-lomba.kelola', $lomba->id) }}" class="btn btn-sm btn-primary">Kelola</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus lomba ini?");
    }
</script>
@endsection
