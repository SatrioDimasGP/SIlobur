@extends('layouts.app')

@section('title')
    Scan QR Code
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4 mb-3">
            <div id="reader" class="p-5 profile-card bg-white border rounded-3 text-center">

            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="p-5 profile-card bg-white border rounded-3">
                <table id="qrcode_table" class="table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">Nama Peserta</th>
                            <th class="text-center">Jenis Burung</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Waktu Scan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($data as $key => $item)
                            @php
                                $burung = optional(optional($item->transaksi?->pemesanans)->burung);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $item->user->name }}</td>
                                <td class="text-center">{{ $burung->jenisBurung->nama ?? '-' }}</td>
                                <td class="text-center">{{ $burung->kelas->nama ?? '-' }}</td>
                                <td class="text-center">{{ $item->updated_at->format('d/m/Y, H.i.s') }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
@endpush

@push('js')
    <script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
    <script>
        async function scan(id_qr) {
            try {
                const response = await fetch(`{{ url('scan-qr') }}/${id_qr}`);

                if (!response.ok) {
                    console.error('Network response was not ok ' + response.statusText);
                    return;
                }

                const data = await response.json();
                console.log(data.data)

                if (data.status == 200) {
                    var notyf = new Notyf();
                    notyf.success('Berhasil melakukan scan!');

                    table.clear().draw();
                    data.data.forEach((item, index) => {
                        const date = new Date(item.updated_at);
                        const formattedDate = date.toLocaleString();

                        table.row.add([
                            item.ref_peserta.user.name,
                            formattedDate
                        ]).draw();
                    });

                } else {
                    var notyf = new Notyf();
                    notyf.error('QRCode tidak ditemukan!');
                }

            } catch (error) {
                var notyf = new Notyf();
                notyf.error('Terjadi kesalahan!');
                console.error('There was a problem with the fetch operation:', error);
            }
        }

        let html5QRCodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: {
                    width: 200,
                    height: 200,
                },
            }
        );

        let scanningEnabled = true;

        function onScanSuccess(decodedText, decodedResult) {
            if (scanningEnabled) {
                scanningEnabled = false;
                scan(decodedResult.decodedText);

                setTimeout(() => {
                    scanningEnabled = true;
                }, 3000);
            }
        }

        html5QRCodeScanner.render(onScanSuccess);
    </script>

    <script>
        let table = $('#qrcode_table').DataTable({
            columnDefs: [{
                className: 'text-center',
                targets: [0, 1]
            }]
        });;

        async function scan(id_qr) {
            try {
                // alert(id_qr)
                const response = await fetch(`{{ url('scan-qr') }}/${id_qr}`);

                if (!response.ok) {
                    console.error('Network response was not ok ' + response.statusText);
                    return;
                }

                const data = await response.json();
                console.log(data.data)

                if (data.status == 200) {
                    var notyf = new Notyf();
                    notyf.success('Berhasil melakukan scan!');
                    // setTimeout(()=>{
                    //     console.log(item);
                    // }, 2000);
                    table.clear().draw();
                    data.data.forEach((item, index) => {
                        const date = new Date(item.updated_at);
                        const formattedDate = date.toLocaleString();

                        table.row.add([
                            item.user.name,
                            item.transaksi?.pemesanans?.burung?.jenis_burung?.nama ?? '-',
                            item.transaksi?.pemesanans?.burung?.kelas?.nama ?? '-',
                            formattedDate
                        ]).draw();
                    });

                } else {
                    var notyf = new Notyf();
                    notyf.error('QRCode tidak ditemukan!');
                }

            } catch (error) {
                var notyf = new Notyf();
                notyf.error('Terjadi kesalahan!');
                console.error('There was a problem with the fetch operation:', error);
            }
        }
    </script>
@endpush
