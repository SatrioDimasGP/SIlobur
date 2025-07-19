@extends('layouts.app')

@push('css')
    <style>
        #daftar-gantangan-wrapper {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 5px;
            margin-bottom: 20px;
        }

        #daftar-gantangan>div {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .gantangan-btn {
            width: 60px;
            height: 60px;
            font-size: 18px;
            justify-content: center;
            align-items: center;
            display: flex;
            border-radius: 10px;
            white-space: nowrap;
        }

        #cart-popup {
            position: fixed;
            top: 80px;
            right: 0;
            width: 320px;
            max-height: 80%;
            overflow-y: auto;
            background-color: white;
            border-left: 2px solid #ccc;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            z-index: 1050;
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h4>Pendaftaran Lomba: {{ $lomba->nama }}</h4>
            <button class="btn btn-primary" onclick="toggleCart()">🛒 Keranjang (<span id="cart-count">0</span>)</button>
        </div>

        <div id="cart-popup">
            <h5>Daftar Pesanan</h5>
            <ul id="cart-items" class="list-group mb-3"></ul>
            <form id="final-submit-form" action="{{ route('daftar.store', $lomba->id) }}" method="POST">
                @csrf
                <input type="hidden" name="nama" id="cart-nama">
                <div id="final-inputs"></div>
                <button type="submit" class="btn btn-success w-100">Submit Semua</button>
            </form>
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" name="nama_input" id="nama_input" class="form-control"
                value="{{ old('nama', Auth::user()->name) }}" required>
        </div>
        <div class="mb-3">
            <label for="jenis_burung_id" class="form-label">Jenis Burung</label>
            <select id="jenis_burung_id" class="form-select">
                <option value="">-- Pilih Jenis Burung --</option>
                @foreach ($jenisBurungs as $jenis)
                    <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="kelas_id" class="form-label">Kelas</label>
            <select id="kelas_id" class="form-select">
                <option value="">-- Pilih Kelas --</option>
            </select>
        </div>

        <div id="pilihan-gantangan" class="mb-3" style="display: none;">
            <label class="form-label">Pilih Gantangan</label>
            <div id="daftar-gantangan-wrapper">
                <div id="daftar-gantangan"></div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const jenisSelect = document.getElementById('jenis_burung_id');
        const kelasSelect = document.getElementById('kelas_id');
        const daftarGantangan = document.getElementById('daftar-gantangan');
        const pilihanGantangan = document.getElementById('pilihan-gantangan');
        const cartItems = document.getElementById('cart-items');
        const finalInputs = document.getElementById('final-inputs');
        const cartCount = document.getElementById('cart-count');

        let cart = [];
        let selectedMap = {};

        function toggleCart() {
            const cartPopup = document.getElementById('cart-popup');
            cartPopup.style.display = cartPopup.style.display === 'block' ? 'none' : 'block';
        }

        jenisSelect.addEventListener('change', () => {
            const jenisId = jenisSelect.value;
            kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
            daftarGantangan.innerHTML = '';
            pilihanGantangan.style.display = 'none';
            if (!jenisId) return;

            fetch(`/ajax/gantangan?jenis_burung_id=${jenisId}&lomba_id={{ $lomba->id }}`)
                .then(res => res.json())
                .then(data => {
                    data.kelas.forEach(k => {
                        const option = document.createElement('option');
                        option.value = k.id;
                        option.textContent = k.nama;
                        kelasSelect.appendChild(option);
                    });
                });
        });

        kelasSelect.addEventListener('change', loadGantangan);

        function loadGantangan() {
            const jenisId = jenisSelect.value;
            const kelasId = kelasSelect.value;
            if (!jenisId || !kelasId) return;

            const key = `${jenisId}-${kelasId}`;

            fetch(`/ajax/gantangan?jenis_burung_id=${jenisId}&kelas_id=${kelasId}&lomba_id={{ $lomba->id }}`)
                .then(res => res.json())
                .then(data => {
                    daftarGantangan.innerHTML = '';

                    // ✅ Tampilkan box dan pesan jika kosong
                    if (data.gantangans.length === 0) {
                        pilihanGantangan.style.display = 'block';
                        daftarGantangan.innerHTML =
                            '<p class="text-muted">Tidak ada nomor gantangan tersedia untuk kombinasi ini.</p>';
                        return;
                    }

                    // ✅ Jika ada gantangan, proses dan tampilkan seperti biasa
                    pilihanGantangan.style.display = 'block';
                    data.gantangans.sort((a, b) => a.nomor - b.nomor);

                    const perRow = 4;
                    const total = data.gantangans.length;
                    const rows = Math.ceil(total / perRow);
                    let matrix = Array.from({
                        length: rows
                    }, () => new Array(perRow).fill(null));

                    data.gantangans.forEach((item, index) => {
                        const rowIdx = rows - 1 - Math.floor(index / perRow);
                        const colIdx = index % perRow;
                        if ((rows - 1 - rowIdx) % 2 === 0) {
                            matrix[rowIdx][colIdx] = item;
                        } else {
                            matrix[rowIdx][perRow - 1 - colIdx] = item;
                        }
                    });

                    matrix.forEach(rowItems => {
                        const row = document.createElement('div');
                        rowItems.forEach(item => {
                            if (!item) return;
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = item.nomor;
                            btn.className = 'btn btn-sm gantangan-btn';

                            const isSelected = (selectedMap[key] || []).includes(item.id);
                            if (item.terisi) {
                                btn.classList.add('btn-secondary');
                                btn.disabled = true;
                            } else if (isSelected) {
                                btn.classList.add('btn-success');
                            } else {
                                btn.classList.add('btn-info');
                            }

                            if (!item.terisi) {
                                btn.addEventListener('click', () => {
                                    const selected = selectedMap[key] || [];
                                    const index = selected.indexOf(item.id);

                                    if (index === -1) {
                                        selected.push(item.id);
                                        selectedMap[key] = selected;
                                        cart.push({
                                            id: item.id,
                                            nomor: item.nomor,
                                            jenis_id: jenisId,
                                            jenis_nama: jenisSelect.options[jenisSelect
                                                .selectedIndex].text,
                                            kelas_id: kelasId,
                                            kelas_nama: kelasSelect.options[kelasSelect
                                                .selectedIndex].text
                                        });
                                        btn.classList.replace('btn-info', 'btn-success');
                                    } else {
                                        selected.splice(index, 1);
                                        selectedMap[key] = selected;
                                        cart = cart.filter(c => !(c.id === item.id && c
                                            .jenis_id === jenisId && c.kelas_id ===
                                            kelasId));
                                        btn.classList.replace('btn-success', 'btn-info');
                                    }

                                    renderCart();
                                });
                            }

                            row.appendChild(btn);
                        });
                        daftarGantangan.appendChild(row);
                    });
                });
        }

        function renderCart() {
            cartItems.innerHTML = '';
            finalInputs.innerHTML = '';
            cartCount.textContent = cart.length;

            cart.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                <div>
                    <strong>${item.nomor}</strong> - ${item.jenis_nama} (${item.kelas_nama})
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">🗑️</button>
            `;
                cartItems.appendChild(li);

                finalInputs.innerHTML += `
                <input type="hidden" name="items[${index}][gantangan_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][jenis_id]" value="${item.jenis_id}">
                <input type="hidden" name="items[${index}][kelas_id]" value="${item.kelas_id}">
            `;
            });

            document.getElementById('cart-nama').value = document.getElementById('nama_input').value;
        }

        function removeFromCart(index) {
            const item = cart[index];
            const key = `${item.jenis_id}-${item.kelas_id}`;
            const selected = selectedMap[key] || [];
            const idx = selected.indexOf(item.id);
            if (idx !== -1) selected.splice(idx, 1);
            selectedMap[key] = selected;

            cart.splice(index, 1);
            renderCart();
            loadGantangan();
        }
    </script>
@endpush
