<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KIB A - {{ ucfirst($lokasi) }}</title>
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
        <h6 class="text-center font-weight-bold uppercase">KARTU INVENTARIS BARANG (KIB) A</h6>
        <h6 class="text-center font-weight-bold mb-4">TANAH - {{ strtoupper($lokasi) }}</h6>
        
        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Kode Barang</th>
                    <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">Register</th>
                    <th rowspan="2">Luas (M2)</th>
                    <th rowspan="2">Alamat/Lokasi</th>
                    <th colspan="4">Bukti Kepemilikan</th>
                    <th rowspan="2">Harga Perolehan (Rp)</th>
                    <th rowspan="2">Cara Perolehan</th>
                    <th rowspan="2">Thn</th>
                    <th rowspan="2">Ket</th>
                </tr>
                <tr>
                    <th>Dokumen</th>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Nama Pemilik</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataTanah as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->nomor_register }}</td>
                        <td class="text-center">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>{{ $item->Lok }}</td>
                        <td>{{ $item->bukti_nama }}</td>
                        <td>{{ $item->bukti_nomor }}</td>
                        <td class="text-center">{{ $item->bukti_tanggal ? \Carbon\Carbon::parse($item->bukti_tanggal)->format('d/m/y') : '-' }}</td>
                        <td>{{ $item->nama_kepemilikan_dokumen }}</td>
                        <td class="text-right">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                        <td>{{ $item->cara_perolehan }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_perolehan)->format('Y') }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="font-weight-bold bg-light">
                <tr>
                    <td colspan="10" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($dataTanah->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>