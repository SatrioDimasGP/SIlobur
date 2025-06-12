@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">Penilaian Ajuan</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('kelas_id'))
            <script>
                window.onload = function() {
                    const kelasId = {{ session('kelas_id') }};
                    const jenisId = {{ session('jenis_id') }};
                    const lombaId = {{ session('lomba_id') }};
                    const jenisSelect = document.getElementById('jenis_burung_id');
                    const kelasSelect = document.getElementById('kelas_id');

                    jenisSelect.value = jenisId

                    kelasSelect.disabled = true
                    kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';

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
                            kelasSelect.value = kelasId
                        })
                        .catch(() => alert('Gagal memuat data kelas'));

                    const blokList = document.getElementById('blok-list'); // Pastikan ada ID-nya di elemen

                    if (!blokList) return;
                    blokList.innerHTML = '';

                    if (!kelasId || !jenisId) return;

                    fetch(`/ajax/blok?jenis_burung_id=${jenisId}&kelas_id=${kelasId}&lomba_id=${lombaId}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.bloks.length === 0) {
                                blokList.innerHTML = '<p class="text-muted">Tidak ada blok tersedia.</p>';
                                return;
                            }

                            const tbl = document.createElement('table');
                            tbl.className = 'table-auto w-full bg-white shadow-md rounded-lg';
                            tbl.innerHTML = `
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-2">Nama Blok</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.bloks.map(blok => `
                                                                            <tr class="border-t">
                                                                                <td class="px-4 py-2">${blok.nama}</td>
                                                                                <td class="px-4 py-2">
                                                                                    ${blok.sudah_dinilai ? `
                                        <button class="bg-gray-400 text-white font-bold py-2 px-4 rounded w-auto cursor-not-allowed" disabled>
                                            Sudah Dinilai
                                        </button>
                                    ` : `
                                        <button type="button"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-auto"
                                            onclick="window.location.href='/penilaian-ajuan/${lombaId}/${blok.id}?jenis_burung_id=${jenisId}&kelas_id=${kelasId}'">
                                            Lakukan Penilaian
                                        </button>
                                    `}
                                                                                </td>
                                                                            </tr>
                                                                        `).join('')}
                    </tbody>
                `;
                            blokList.appendChild(tbl);
                        })
                        .catch(() => alert('Gagal memuat data blok'));
                };
            </script>
        @endif



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
            <!-- Daftar blok akan muncul di sini -->
        </div>
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

        // Saat pilih kelas ➔ load blok
        // Saat pilih kelas ➔ load blok
        // Saat pilih kelas ➔ load blok
        kelasSelect.addEventListener('change', () => {
            const kelasId = kelasSelect.value;
            const jenisId = jenisSelect.value;
            const lombaId = {{ $lomba->id }};
            blokList.innerHTML = '';

            if (!kelasId || !jenisId) return;

            fetch(`/ajax/blok?jenis_burung_id=${jenisId}&kelas_id=${kelasId}&lomba_id={{ $lomba->id }}`)
                .then(r => r.json())
                .then(data => {
                    if (data.bloks.length === 0) {
                        blokList.innerHTML = '<p class="text-muted">Tidak ada blok tersedia.</p>';
                        return;
                    }

                    const tbl = document.createElement('table');
                    tbl.className = 'table-auto w-full bg-white shadow-md rounded-lg';
                    tbl.innerHTML = `
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-2">Nama Blok</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            ${data.bloks.map(blok => `
                                    <tr class="border-t">
                                        <td class="px-4 py-2">${blok.nama}</td>
                                        <td class="px-4 py-2">
                                            ${blok.sudah_dinilai ? `
                            <button class="bg-gray-400 text-white font-bold py-2 px-4 rounded w-auto cursor-not-allowed" disabled>
                                Sudah Dinilai
                            </button>
                        ` : `
                            <button type="button"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-auto"
                                onclick="window.location.href='/penilaian-ajuan/{{ $lomba->id }}/${blok.id}?jenis_burung_id=${jenisId}&kelas_id=${kelasId}'">
                                Lakukan Penilaian
                            </button>
                        `}
                                        </td>
                                    </tr>
                                `).join('')}
        </tbody>
    `;
                    blokList.appendChild(tbl);
                })
                .catch(() => alert('Gagal memuat data blok'));
        });
    </script>
@endsection
