@extends('layouts.app')

@section('title','Pembayaran')

@section('content')
    <div class="alert alert-warning" role="alert">
        <ul>
            <li>
                Apabila sudah melakukan pembayaran, tunggu sekitar 5 sampai 10 detik, atau klik "Cek Status"
            </li>
            <li>
                <b>Jangan tinggalkan halaman ini</b> sampai anda mendapatkan
                notifikasi bahwa pembayaran berhasil dilakukan
            </li>
        </ul>
    </div>

    <div class="d-flex justify-content-center">
        <div class="p-5 profile-card bg-white border rounded-3 text-center d-flex flex-column gap-2">
            <span>ANDA AKAN MELAKUKAN PEMBAYARAN</span>
            <span><b>TIKET SILOBUR 2025</b></span>
            <span>DENGAN HARGA</span>
            <span><b>Rp. {{ number_format($transaksi->total, 0, ',', '.') }}</b></span>
            <button class="btn-grad w-100 mt-4" id="pay-button">Lakukan Pembayaran</button>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ config('midtrans.snapUrl') }}" data-client-key="{{ config('midtrans.clientKey') }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            // SnapToken acquired from previous step
            // alert('{{ $transaksi->snap_token }}')
            snap.pay('{{ $transaksi->snap_token }}', {
                // Optional
                onSuccess: function(result) {
                    window.location.href = '{{ route('pembayaran.sukses') }}';
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                }
            });
        };
    </script>
@endpush
