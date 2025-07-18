<!DOCTYPE html>
<html>

<head>
    <title>Bukti Pembayaran</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            margin-bottom: 20px;
        }

        .qr {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h2>Bukti Pembayaran</h2>
    <p>Nomor Transaksi: {{ $transaksi->order_id }}</p>
    <p>Total: Rp{{ number_format($transaksi->total, 0, ',', '.') }}</p>
    <p>Tanggal: {{ $transaksi->tanggal_transaksi }}</p>
    <p>Metode Pembayaran: {{ $transaksi->metode_pembayaran }}</p>
    @if ($qrcodePath)
        @if ($qrcodePath)
            @php
                $qrData = base64_encode(file_get_contents($qrcodePath));
                $qrMime = mime_content_type($qrcodePath); // biasanya image/png
            @endphp
            <img src="data:{{ $qrMime }};base64,{{ $qrData }}" width="150" height="150" alt="QR Code">
        @endif

        {{-- <img src="file://{{ $qrcodePath }}" width="150" height="150" alt="QR Code"> --}}
    @endif



</body>

</html>
