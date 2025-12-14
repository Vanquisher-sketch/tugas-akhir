<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Data Peralatan & Mesin - {{ ucfirst($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-size: 9pt; }
        .table th, .table td { padding: 0.2rem; vertical-align: middle; border: 1px solid #000 !important; }
        .header-table td { border: none !important; padding: 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-block { margin-top: 40px; }
        .signature-block .signer { margin-top: 50px; }
        @page { size: A4 landscape; margin: 1cm; }
    </style>
</head>
<body onload="window.print()">

    <div class="container-fluid">
        <h6 class="text-center font-weight-bold">DAFTAR BMD PADA KUASA PENGGUNA BARANG</h6>
        <h6 class="text-center font-weight-bold">PERALATAN DAN MESIN</h6>
        
        <table class="table header-table mt-4" style="width: 50%;">
             <tbody>
                <tr><td><b>Kuasa Pengguna Barang</b></td><td>: ............................................</td></tr>
                <tr><td><b>Kode Lokasi</b></td><td>: ............................................</td></tr>
                <tr><td><b>PROVINSI/KABUPATEN/KOTA</b></td><td>: ............................................</td></tr>
                <tr><td><b>TAHUN</b></td><td>: {{ date('Y') }}</td></tr>
            </tbody>
        </table>

        <table class="table table-bordered mt-3">
            <thead class="text-center">
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Kode Barang</th>
                    <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">NIBAR</th>
                    <th rowspan="2">Nomor Register</th>
                    <th colspan="3">Spesifikasi Barang</th>
                    <th colspan="4">Kendaraan (Diisi*)</th>
                    <th rowspan="2">Jumlah</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Harga Satuan (Rp)</th>
                    <th rowspan="2">Nilai Perolehan (Rp)</th>
                    <th rowspan="2">Cara Perolehan</th>
                    <th rowspan="2">Tgl Perolehan</th>
                    <th rowspan="2">Status Penggunaan</th>
                    <th rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <th>Merek/Tipe</th><th>Ukuran</th><th>Lainnya</th><th>No. Rangka</th><th>No. Mesin</th><th>No. Polisi</th><th>BPKB</th>
                </tr>
                 <tr>
                    @for ($i = 1; $i <= 20; $i++) {{-- Menyesuaikan dengan jumlah kolom data --}}
                        <th>({{ $i+5 }})</th> {{-- Nomor kolom dimulai dari (6) --}}
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse ($dataPeralatan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_barang }}</td><td>{{ $item->nama_barang }}</td><td>{{ $item->nibar }}</td><td>{{ $item->nomor_register }}</td><td>{{ $item->merek_tipe }}</td><td>{{ $item->ukuran }}</td><td>{{ $item->spesifikasi_lainnya }}</td><td>{{ $item->nomor_rangka }}</td><td>{{ $item->nomor_mesin }}</td><td>{{ $item->nomor_polisi }}</td><td>{{ $item->nomor_bpkb }}</td><td class="text-center">{{ $item->jumlah }}</td><td>{{ $item->satuan }}</td><td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td><td class="text-right">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td><td>{{ $item->cara_perolehan }}</td><td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '' }}</td><td>{{ $item->status_penggunaan }}</td><td>{{ $item->keterangan }}</td>
                </tr>
                @empty
                <tr><td colspan="20" class="text-center">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="row signature-block">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p>Kuasa Pengguna Barang</p>
                <div class="signer"><p class="font-weight-bold"><u>............................................</u></p><p>NIP. ............................................</p></div>
            </div>
        </div>
    </div>

</body>
</html>