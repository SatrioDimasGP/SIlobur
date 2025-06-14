@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Penilaian Koncer - Lomba {{ $lomba->nama }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- @if (isset($menunggu) && $menunggu)
            <div class="alert alert-warning text-center">
                Semua juri belum menyelesaikan penilaian tahap Ajuan.
            </div>
        @endif --}}

        {{-- @elseif(isset($nomorLolosKoncer) && count($nomorLolosKoncer) == 0)
    <div class="alert alert-info text-center">
        Tidak ada nomor yang lolos ke tahap Koncer.
    </div> --}}
        {{-- @endif --}}

        <div class="mb-4">
            <label for="jenis_burung_id" class="form-label">Jenis Burung</label>
            <select id="jenis_burung_id" class="form-select">
                <option value="">-- Pilih Jenis Burung --</option>
                @foreach ($jenisBurungs as $jenis)
                    <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="kelas_id" class="form-label">Kelas</label>
            <select id="kelas_id" class="form-select" disabled>
                <option value="">-- Pilih Kelas --</option>
            </select>
        </div>

        <div id="blok-list" class="mt-5">
            <!-- Form penilaian koncer akan muncul di sini -->
        </div>

        <script>
            const jenisSelect = document.getElementById('jenis_burung_id');
            const kelasSelect = document.getElementById('kelas_id');
            const blokList = document.getElementById('blok-list');

            // Saat pilih jenis burung ➔ load kelas
            jenisSelect.addEventListener('change', () => {
                const jenisId = jenisSelect.value;
                kelasSelect.disabled = true;
                kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
                blokList.innerHTML = '';

                if (!jenisId) return;

                fetch(`/ajax/kelas?jenis_burung_id=${jenisId}`)
                    .then(r => r.json())
                    .then(data => {
                        data.kelas.forEach(kls => {
                            const opt = document.createElement('option');
                            opt.value = kls.id;
                            opt.textContent = kls.nama;
                            kelasSelect.appendChild(opt);
                        });
                        kelasSelect.disabled = false;
                    })
                    .catch(() => alert('Gagal memuat data kelas'));
            });

            // Saat pilih kelas ➔ load nomor lolos koncer dan tampilkan form
            kelasSelect.addEventListener('change', () => {
                const kelasId = kelasSelect.value;
                const jenisId = jenisSelect.value;
                blokList.innerHTML = '';

                if (!kelasId || !jenisId) return;

                fetch(
                        `/ajax/nomor-lolos-koncer?jenis_burung_id=${jenisId}&kelas_id=${kelasId}&lomba_id={{ $lomba->id }}`)
                    .then(r => r.json())
                    .then(data => {
                        blokList.innerHTML = '';

                        if (data.sudahMenilai) {
                            blokList.innerHTML = `
                            <div class="alert alert-success text-center">
                                Anda sudah melakukan penilaian pada tahap Koncer untuk jenis burung dan kelas ini.
                            </div>`;
                            return;
                        }

                        if (data.menunggu) {
                            blokList.innerHTML = `
        <div class="alert alert-warning text-center">
            Semua juri belum menyelesaikan penilaian tahap Ajuan untuk jenis burung dan kelas ini.
        </div>`;
                            return;
                        }

                        // Buat form dengan hidden input jenis_burung_id dan kelas_id
                        let formHTML = `
                        <form action="{{ route('penilaian-koncer.store', $lomba->id) }}" method="POST" id="formPenilaianKoncer">
                            @csrf
                            <input type="hidden" name="lomba_id" value="{{ $lomba->id }}">
                            <input type="hidden" name="jenis_burung_id" id="jenis_burung_id_hidden" value="">
                            <input type="hidden" name="kelas_id" id="kelas_id_hidden" value="">
                            <div class="row">`;

                        data.nomorLolosKoncer.forEach((blok) => {
                            formHTML += `
                            <div class="col-md-4 mb-4">
                                <div class="card text-center shadow-sm">
                                    <div class="card-body p-2">
                                        <h5 class="card-title mb-3">Nomor: ${blok.gantangan.nomor}</h5>
                                        <div class="d-flex justify-content-around">
                                            <label>
                                                <input type="checkbox" name="penilaian[${blok.id}][bendera][]" value="${blok.bendera_merah_id}"> Merah
                                            </label>
                                            <label>
                                                <input type="checkbox" name="penilaian[${blok.id}][bendera][]" value="${blok.bendera_biru_id}"> Biru
                                            </label>
                                            <label>
                                                <input type="checkbox" name="penilaian[${blok.id}][bendera][]" value="${blok.bendera_kuning_id}"> Kuning
                                            </label>
                                        </div>
                                        <input type="hidden" name="penilaian[${blok.id}][gantanganId]" value="${blok.id}">
                                    </div>
                                </div>
                            </div>`;
                        });

                        formHTML += `
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Submit Penilaian</button>
                            </div>
                        </form>`;

                        blokList.innerHTML = formHTML;

                        // Setelah form dibuat, tambahkan event listener submit untuk isi hidden input
                        const formPenilaian = document.getElementById('formPenilaianKoncer');
                        const jenisBurungHidden = document.getElementById('jenis_burung_id_hidden');
                        const kelasHidden = document.getElementById('kelas_id_hidden');

                        formPenilaian.addEventListener('submit', function(e) {
                            jenisBurungHidden.value = jenisSelect.value;
                            kelasHidden.value = kelasSelect.value;
                        });
                    })
                    .catch(() => alert('Gagal memuat data nomor lolos koncer'));
            });
        </script>
    </div>
@endsection
