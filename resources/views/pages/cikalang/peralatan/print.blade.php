<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KIB B - {{ ucfirst($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @page { size: A4 landscape; margin: 1cm; }
        body { font-family: 'Times New Roman', serif; font-size: 8pt; color: #000; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; vertical-align: middle; padding: 3px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header-info td { border: none !important; padding: 0; font-weight: bold; }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <h6 class="text-center font-weight-bold mb-0">DAFTAR BMD PADA KUASA PENGGUNA BARANG</h6>
        <h6 class="text-center font-weight-bold mb-4">PERALATAN DAN MESIN (KIB B)</h6>
        
        <table class="header-info mb-3" style="width: 40%;">
            <tr><td width="150">Lokasi</td><td>: {{ strtoupper($lokasi) }}</td></tr>
            <tr><td>Kabupaten/Kota</td><td>: TASIKMALAYA</td></tr>
            <tr><td>Tahun</td><td>: {{ date('Y') }}</td></tr>
        </table>

        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Kode Barang</th>
                    <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">NIBR</th>
                    <th rowspan="2">Register</th>
                    <th colspan="2">Spesifikasi</th>
                    <th colspan="3">Nomor Kendaraan</th>
                    <th rowspan="2">Jml</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Harga Perolehan (Rp)</th>
                    <th rowspan="2">Tgl Perolehan</th>
                    <th rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <th>Merk/Tipe</th>
                    <th>Lainnya</th>
                    <th>Polisi</th>
                    <th>Rangka</th>
                    <th>BPKB</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataPeralatan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->nibr }}</td>
                    <td>{{ $item->nomor_register }}</td>
                    <td>{{ $item->merk_tipe }}</td>
                    <td>{{ $item->spesifikasi_lainnya }}</td>
                    <td class="text-center">{{ $item->nomor_polisi }}</td>
                    <td><small>{{ $item->nomor_rangka }}</small></td>
                    <td>{{ $item->nomor_bpkb }}</td>
                    <td class="text-center">{{ $item->jumlah }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td class="text-right">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d/m/Y') : '' }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @empty
                <tr><td colspan="15" class="text-center">Data Kosong</td></tr>
                @endforelse
            </tbody>
            <tfoot class="font-weight-bold">
                <tr>
                    <td colspan="12" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($dataPeralatan->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Tasikmalaya, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p>Kuasa Pengguna Barang</p>
                <br><br><br>
                <p class="font-weight-bold"><u>............................................</u></p>
                <p>NIP. ............................................</p>
            </div>
        </div>
    </div>
</body>
</html>