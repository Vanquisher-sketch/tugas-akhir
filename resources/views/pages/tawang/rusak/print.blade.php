<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Daftar Barang Rusak Berat - {{ ucfirst($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; }
        .table-bordered th, .table-bordered td { border: 1px solid black !important; vertical-align: middle; font-size: 10px; padding: 4px; }
        thead { background-color: #e9ecef; }
        @media print {
            @page { size: landscape; margin: 0.5cm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <div class="text-center mb-4">
            <h5 class="mb-0">DAFTAR BARANG RUSAK BERAT</h5>
            <h6>SKPD: KECAMATAN {{ strtoupper($lokasi) }}</h6>
        </div>
        <table class="table table-bordered table-sm">
            <thead class="text-center">
                <tr>
                    <th>No. Urut SKPD</th>
                    <th>No. ID Pemda</th>
                    <th>Nama / Jenis Barang</th>
                    <th>Spesifikasi</th>
                    <th>No. Polisi</th>
                    <th>Tahun Perolehan</th>
                    <th>Harga Perolehan (Rp)</th>
                    <th>Kondisi</th>
                    <th>Tercatat di KIB</th>
                    <th>Ket.</th>
                </tr>
                 <tr>
                    @for ($i = 1; $i <= 10; $i++)
                        <th>({{ $i }})</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse ($dataRusak as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->no_id_pemda }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->spesifikasi }}</td>
                    <td>{{ $item->no_polisi }}</td>
                    <td class="text-center">{{ $item->tahun_perolehan }}</td>
                    <td class="text-right">{{ number_format($item->harga_perolehan, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $item->kondisi }}</td>
                    <td>{{ $item->tercatat_di_kib }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">Belum ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>

