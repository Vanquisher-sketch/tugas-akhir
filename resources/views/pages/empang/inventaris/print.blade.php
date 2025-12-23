<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Inventaris Ruangan - {{ $room->name }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-size: 10pt; }
        .table th, .table td { padding: 0.25rem; vertical-align: middle; border: 1px solid #000 !important; }
        .header-table td { border: none !important; padding: 0.1rem 0; }
        .text-center { text-align: center; }
        .signature-block { margin-top: 40px; page-break-inside: avoid; }
        .signature-block .signer { margin-top: 60px; }
        @page { size: A4 landscape; margin: 1cm; }
        h5, h6 { margin-bottom: 0.5rem; }
    </style>
</head>
<body onload="window.print()">

    <div class="container-fluid">
        <h5 class="text-center font-weight-bold">KARTU INVENTARIS BARANG (KIB) RUANGAN</h5>
        <h6 class="text-center">TAHUN ANGGARAN {{ date('Y') }}</h6>

        <table class="table header-table mt-4" style="width: 50%;">
             <tbody>
                <tr>
                    <td style="width: 30%;"><b>SKPD</b></td>
                    <td>: ............................................</td>
                </tr>
                <tr>
                    <td><b>UNIT KERJA</b></td>
                    <td>: {{ strtoupper($lokasi) }}</td>
                </tr>
                 <tr>
                    <td><b>RUANGAN</b></td>
                    <td>: {{ strtoupper($room->name) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered mt-3">
            <thead class="text-center">
                <tr>
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">NIBAR</th>
                    <th rowspan="2" class="align-middle">Nomor Register</th>
                    <th rowspan="2" class="align-middle">Kode Barang</th>
                    <th rowspan="2" class="align-middle">Nama Barang</th>
                    <th rowspan="2" class="align-middle">Spesifikasi Nama Barang</th>
                    <th colspan="2">Spesifikasi Barang</th>
                    <th rowspan="2" class="align-middle">Jumlah</th>
                    <th rowspan="2" class="align-middle">Satuan</th>
                    <th rowspan="2" class="align-middle">Ket.</th>
                </tr>
                <tr>
                    <th>Merek/Tipe</th>
                    <th>Tahun Perolehan</th>
                </tr>
                <tr>
                    @for ($i = 1; $i <= 11; $i++)
                        <th>({{ $i+4 }})</th> {{-- Nomor kolom dimulai dari (5) --}}
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse ($dataInventaris as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nibar }}</td>
                    <td>{{ $item->nomor_register }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->spesifikasi_barang }}</td>
                    <td>{{ $item->merk_tipe }}</td>
                    <td class="text-center">{{ $item->tahun_perolehan }}</td>
                    <td class="text-center">{{ $item->jumlah }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        <small><i>Catatan : Tidak dibenarkan memindahkan barang-barang yang ada pada daftar barang ini tanpa sepengetahuan pengurus barang pengguna/pengurus barang pembantu dan penanggungjawab ruangan.</i></small>
        
        <div class="row signature-block">
            <div class="col-4 text-center">
                <p>Mengetahui,</p>
                <p>Kepala {{ ucfirst($lokasi) }}</p>
                <div class="signer"><p class="font-weight-bold"><u>............................................</u></p><p>NIP. ............................................</p></div>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <p>Tasikmalaya, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p>Penanggung Jawab Ruangan</p>
                <div class="signer"><p class="font-weight-bold"><u>............................................</u></p><p>NIP. ............................................</p></div>
            </div>
        </div>
    </div>
</body>
</html>