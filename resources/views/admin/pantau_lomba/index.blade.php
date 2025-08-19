@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('title', 'Pantau Penilaian')

@section('content')
    @php
        $tahapId = request('tahap_id', 1);
        $labels =
            $tahapId == 1
                ? ['Hijau' => 'success', 'Putih' => 'secondary', 'Hitam' => 'dark']
                : ['Merah' => 'danger', 'Biru' => 'primary', 'Kuning' => 'warning text-dark'];
    @endphp

    <div class="container-fluid">
        <h4 class="mb-4">Pantau Penilaian</h4>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('admin.pantau_lomba') }}" class="row mb-4 g-3">
            {{-- Pilih Lomba --}}
            <div class="col-md-3">
                <select name="lomba_id" id="lomba_id" class="form-select" required>
                    <option value="">Pilih Lomba</option>
                    @foreach ($listLomba as $lomba)
                        <option value="{{ $lomba->id }}" {{ request('lomba_id') == $lomba->id ? 'selected' : '' }}>
                            {{ $lomba->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Jenis Burung --}}
            <div class="col-md-3">
                <select name="burung_id" id="burung_id" class="form-select" required>
                    <option value="">Pilih Jenis Burung</option>
                </select>
            </div>

            {{-- Pilih Kelas --}}
            <div class="col-md-3">
                <select name="kelas_id" id="kelas_id" class="form-select" required>
                    <option value="">Pilih Kelas</option>
                </select>
            </div>

            {{-- Tahap --}}
            <div class="col-md-3">
                <select name="tahap_id" id="tahap_id" class="form-select" required>
                    <option value="1" {{ $tahapId == 1 ? 'selected' : '' }}>Tahap Ajuan</option>
                    <option value="2" {{ $tahapId == 2 ? 'selected' : '' }}>Tahap Koncer</option>
                </select>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0">Hasil Penilaian</h5>
            </div>
            <div class="card-body">
                <table id="datatable-main" class="table table-bordered table-striped text-sm">
                    <thead>
                        <tr id="pantau-thead">
                            <th>No</th>
                            <th>Nama Juri</th>
                            @foreach ($labels as $label => $color)
                                <th>{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="pantau-tbody">
                        {{-- isi awal, nanti akan dioverride JS --}}
                        @foreach ($juriList as $index => $juri)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $juri->name }}</td>
                                @foreach (array_keys($labels) as $label)
                                    <td>
                                        @foreach ($juri->{'penilaian_' . strtolower($label)} ?? [] as $p)
                                            @php
                                                $colorParts = explode(' ', $labels[$label]);
                                                $bg = 'bg-' . $colorParts[0];
                                                $text = $colorParts[1] ?? '';
                                            @endphp
                                            <span
                                                class="badge {{ $bg }} {{ $text }}">{{ $p['nomor'] ?? '-' }}</span>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

    <script>
        function getLabels(tahapId) {
            return tahapId == 1 ? {
                Hijau: 'success',
                Putih: 'secondary',
                Hitam: 'dark'
            } : {
                Merah: 'danger',
                Biru: 'primary',
                Kuning: 'warning text-dark'
            };
        }

        function renderTable(response, tahapId) {
            const labels = getLabels(tahapId);

            // update header
            const theadRow = $('#pantau-thead');
            theadRow.empty().append('<th>No</th><th>Nama Juri</th>');
            Object.keys(labels).forEach(label => {
                theadRow.append(`<th>${label}</th>`);
            });

            // update body
            const tbody = $('#pantau-tbody');
            tbody.empty();
            response.data.forEach((juri, index) => {
                let row = `<tr>
                    <td>${index + 1}</td>
                    <td>${juri.nama}</td>`;

                Object.keys(labels).forEach(label => {
                    let key = 'penilaian_' + label.toLowerCase();
                    let badges = '';
                    (juri[key] || []).forEach(p => {
                        let colorParts = labels[label].split(' ');
                        let bg = 'bg-' + colorParts[0];
                        let textClass = colorParts[1] ?? '';
                        badges += `<span class="badge ${bg} ${textClass}">${p.nomor}</span> `;
                    });
                    row += `<td>${badges}</td>`;
                });

                row += `</tr>`;
                tbody.append(row);
            });
        }

        function fetchPantauData() {
            const burungId = $('select[name="burung_id"]').val();
            const kelasId = $('select[name="kelas_id"]').val();
            const tahapId = $('select[name="tahap_id"]').val();

            if (!burungId || !kelasId || !tahapId) return;

            $.ajax({
                url: "{{ route('admin.pantau_lomba.data') }}",
                method: "GET",
                data: {
                    burung_id: burungId,
                    kelas_id: kelasId,
                    tahap_id: tahapId
                },
                success: function(response) {
                    renderTable(response, tahapId);
                },
                error: function(err) {
                    console.error('Gagal mengambil data:', err);
                }
            });
        }

        $(document).ready(function() {
            fetchPantauData(); // load awal
            setInterval(fetchPantauData, 5000); // auto refresh tiap 5 detik
        });

        $('#lomba_id').on('change', function() {
            const lombaId = $(this).val();
            $('#burung_id').html('<option value="">Pilih Jenis Burung</option>');
            $('#kelas_id').html('<option value="">Pilih Kelas</option>');
            if (!lombaId) return;

            $.get("{{ route('admin.ajax.burung') }}", {
                lomba_id: lombaId
            }, function(res) {
                res.burung.forEach(b => {
                    $('#burung_id').append(`<option value="${b.id}">${b.nama}</option>`);
                });
            });
        });

        $('#burung_id').on('change', function() {
            const lombaId = $('#lomba_id').val();
            const burungId = $(this).val();
            $('#kelas_id').html('<option value="">Pilih Kelas</option>');
            if (!burungId) return;

            $.get("{{ route('admin.ajax.kelas') }}", {
                lomba_id: lombaId,
                jenis_burung_id: burungId // perbaiki di sini
            }, function(res) {
                res.kelas.forEach(k => {
                    $('#kelas_id').append(`<option value="${k.id}">${k.nama}</option>`);
                });
            });
        });
    </script>
@endpush
