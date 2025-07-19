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
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $item->user->name }}</td>
                                <td class="text-center">
                                    @foreach ($item->transaksi?->pemesanans ?? [] as $p)
                                        {{ $p->burung?->jenisBurung?->nama ?? '-' }}@if (!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @foreach ($item->transaksi?->pemesanans ?? [] as $p)
                                        {{ $p->burung?->kelas?->nama ?? '-' }}@if (!$loop->last), @endif
                                    @endforeach
                                </td>
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
        let table = $('#qrcode_table').DataTable({
            columnDefs: [{
                className: 'text-center',
                targets: '_all'
            }]
        });

        async function scan(id_qr) {
            try {
                const response = await fetch(`{{ url('scan-qr') }}/${id_qr}`);

                if (!response.ok) {
                    console.error('Network response was not ok ' + response.statusText);
                    return;
                }

                const data = await response.json();
                console.log(data.data);

                if (data.status == 200) {
                    const notyf = new Notyf();
                    notyf.success('Berhasil melakukan scan!');

                    table.clear().draw();

                    data.data.forEach((item) => {
                        const date = new Date(item.updated_at);
                        const formattedDate = date.toLocaleString();

                        let jenisList = [];
                        let kelasList = [];

                        (item.transaksi?.pemesanans ?? []).forEach(p => {
                            jenisList.push(p.burung?.jenis_burung?.nama ?? '-');
                            kelasList.push(p.burung?.kelas?.nama ?? '-');
                        });

                        table.row.add([
                            item.user.name,
                            jenisList.join(', '),
                            kelasList.join(', '),
                            formattedDate
                        ]).draw();
                    });

                } else {
                    new Notyf().error('QRCode tidak ditemukan!');
                }

            } catch (error) {
                new Notyf().error('Terjadi kesalahan!');
                console.error('Fetch error:', error);
            }
        }

        let html5QRCodeScanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: {
                width: 200,
                height: 200,
            }
        });

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
@endpush
