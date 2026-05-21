<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barang Rusak - {{ strtoupper($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @page { size: landscape; margin: 10mm; }
        body { font-family: 'Times New Roman', serif; font-size: 9pt; color: #000; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; vertical-align: middle; padding: 5px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <h5 class="text-center font-weight-bold mb-0">DAFTAR BARANG RUSAK BERAT</h5>
        <h6 class="text-center font-weight-bold mb-4">SKPD: KECAMATAN {{ strtoupper($lokasi) }}</h6>
        
        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr>
                    <th>No</th>
                    <th>No. ID Pemda</th>
                    <th>Nama / Jenis Barang</th>
                    <th>Spesifikasi</th>
                    <th>No. Polisi</th>
                    <th>Thn Perolehan</th>
                    <th>Harga Perolehan (Rp)</th>
                    <th>Kondisi</th>
                    <th>Tercatat di KIB</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataRusak as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->no_id_pemda }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->spesifikasi }}</td>
                    <td class="text-center">{{ $item->no_polisi }}</td>
                    <td class="text-center">{{ $item->tahun_perolehan }}</td>
                    <td class="text-right">{{ number_format($item->harga_perolehan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->kondisi }}</td>
                    <td class="text-center">{{ $item->tercatat_di_kib }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>