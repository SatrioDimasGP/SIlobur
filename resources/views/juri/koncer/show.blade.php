@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Penilaian Koncer - Lomba {{ optional($lomba)->nama ?? '-' }}</h1>

        @if (is_null($lomba))
            <div class="alert alert-danger text-center">
                Juri belum ditugaskan di lomba ini.
            </div>
        @else
            {{-- Sisa isi halaman hanya dirender jika juri memang ditugaskan --}}

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Dropdown filter --}}
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

            {{-- Script hanya dijalankan jika juri ditugaskan --}}
            <script>
                const jenisSelect = document.getElementById('jenis_burung_id');
                const kelasSelect = document.getElementById('kelas_id');
                const blokList = document.getElementById('blok-list');

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

                kelasSelect.addEventListener('change', () => {
                    const kelasId = kelasSelect.value;
                    const jenisId = jenisSelect.value;
                    blokList.innerHTML = '';

                    if (!kelasId || !jenisId) return;

                    fetch(
                            `/ajax/nomor-lolos-koncer?jenis_burung_id=${jenisId}&kelas_id=${kelasId}&lomba_id={{ $lomba->id ?? 'null' }}`
                            )
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
                        <label class="badge bg-danger">
                            <input type="radio" name="penilaian[${blok.id}][bendera]" value="${blok.bendera_merah_id}">
                            Merah
                        </label>
                        <label class="badge bg-primary">
                            <input type="radio" name="penilaian[${blok.id}][bendera]" value="${blok.bendera_biru_id}">
                            Biru
                        </label>
                        <label class="badge" style="background-color: #f7e700; color: #000; border: 1px solid #ddd;">
                            <input type="radio" name="penilaian[${blok.id}][bendera]" value="${blok.bendera_kuning_id}">
                            Kuning
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

                            // Aktifkan fitur 'klik dua kali untuk uncheck radio'
                            document.querySelectorAll('#formPenilaianKoncer input[type="radio"]').forEach(radio => {
                                radio.addEventListener('mousedown', function(e) {
                                    if (this.checked) {
                                        // Simpan status sementara
                                        this.wasChecked = true;
                                    } else {
                                        this.wasChecked = false;
                                    }
                                });

                                radio.addEventListener('click', function(e) {
                                    if (this.wasChecked) {
                                        this.checked = false;
                                        // Trigger change supaya nilai di form juga ikut update
                                        this.dispatchEvent(new Event('change'));
                                    }
                                });
                            });


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
        @endif
    </div>

@endsection
