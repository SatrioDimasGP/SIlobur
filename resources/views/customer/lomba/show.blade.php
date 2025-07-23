@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
    @push('js')
        <script>
            @if ($allLunas && $transaksiDenganQr)
                const qrId = "{{ $transaksiDenganQr->qrcode->id }}";

                setInterval(() => {
                    fetch("{{ url('cek-status-qr') }}/" + qrId)
                        .then(response => response.json())
                        .then(data => {
                            console.log("Polling response:", data);
                            if (data.status_qr_id == 2) {
                                const qrSection = document.getElementById('qr-section');
                                qrSection.innerHTML = `
                            <div class="alert alert-success mt-2" id="qr-scanned-message">
                                Barcode sudah discan.
                            </div>
                        `;
                            }
                        })
                        .catch(error => console.error('QR polling error:', error));
                }, 5000);
            @endif
        </script>
    @endpush

    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h4>Detail Pemesanan Anda</h4>
            <div class="card-tools">
                <a href="{{ route('lomba.saya') }}" class="btn btn-tool">
                    <i class="fas fa-arrow-alt-circle-left"></i>
                </a>
            </div>
        </div>

        @php
            $first = $pemesanans->first();
            $groupedBurung = $pemesanans->groupBy(function ($item) {
                return optional($item->burung->jenisBurung)->nama . ' - ' . optional($item->burung->kelas)->nama;
            });
        @endphp

        <div class="card">
            <div class="card-body">
                <p><strong>Nama Lomba:</strong> {{ $first->lomba->nama }}</p>
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($first->lomba->tanggal)->format('d M Y') }}</p>

                <p><strong>Jenis Burung dan Kelas:</strong></p>
                <ul>
                    @foreach ($groupedBurung as $jenisKelas => $items)
                        <li>
                            <strong>{{ $jenisKelas }}</strong><br>
                            No. Gantangan:
                            {{ $items->pluck('gantangan.nomor')->filter()->map(fn($n) => $n)->implode(', ') }}
                        </li>
                    @endforeach
                </ul>

                <p><strong>Status:</strong>
                    <span class="badge {{ $allLunas ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $allLunas ? 'Lunas' : 'Menunggu' }}
                    </span>
                </p>

                <div id="qr-section">
                    @if ($allLunas && $transaksiDenganQr)
                        <hr>
                        <h5>QR Code untuk Verifikasi:</h5>

                        @if ($transaksiDenganQr->qrcode->status_qr_id == 1)
                            <img src="{{ asset('storage/qr_code/' . $transaksiDenganQr->qrcode->file_qrcode) }}"
                                alt="QR Code" width="200" id="qrcode-img">
                        @endif

                        @if ($transaksiDenganQr->qrcode->status_qr_id == 2)
                            <div class="alert alert-success mt-2" id="qr-scanned-message">
                                Barcode sudah discan.
                            </div>
                        @endif
                    @else
                        <form action="{{ route('pembayaran.store') }}" method="POST">
                            @csrf
                           @foreach ($pemesanans as $pesanan)
                                <input type="hidden" name="pemesanan_ids[]" value="{{ $pesanan->id }}">
                            @endforeach
                            <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
                        </form>
                        <hr>
                        <div class="alert alert-info">
                            Pesanan Anda sedang menunggu pembayaran. Silakan lakukan pembayaran.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
