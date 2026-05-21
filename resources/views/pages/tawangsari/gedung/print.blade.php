<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KIB C - {{ strtoupper($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @page { size: landscape; margin: 10mm; }
        body { font-family: 'Times New Roman', serif; font-size: 8pt; color: #000; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; vertical-align: middle; padding: 4px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <h6 class="text-center font-weight-bold mb-0">DAFTAR BMD PADA KUASA PENGGUNA BARANG</h6>
        <h6 class="text-center font-weight-bold mb-4">GEDUNG DAN BANGUNAN (KIB C) - {{ strtoupper($lokasi) }}</h6>
        
        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Gedung</th>
                    <th>NIBAR</th>
                    <th>Reg</th>
                    <th>Lantai</th>
                    <th>Luas (M2)</th>
                    <th>Lokasi/Alamat</th>
                    <th>Status Tanah</th>
                    <th>Tgl Perolehan</th>
                    <th>Nilai Perolehan (Rp)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataGedung as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->nbar }}</td>
                    <td>{{ $item->nomor_register }}</td>
                    <td class="text-center">{{ $item->jumlah_lantai }}</td>
                    <td class="text-center">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $item->Lok }}</td>
                    <td>{{ $item->status_kepemilikan_tanah }}</td>
                    <td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                    <td class="text-right">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="font-weight-bold">
                <tr>
                    <td colspan="10" class="text-right">TOTAL NILAI ASET</td>
                    <td class="text-right">{{ number_format($dataGedung->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>