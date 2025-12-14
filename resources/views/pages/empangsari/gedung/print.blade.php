<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Gedung (KIB C) - {{ ucfirst($lokasi) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px; /* Font kecil agar muat */
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2, .header h3 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Tanda Tangan */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 30%;
            text-align: center;
        }
        .signature-box p {
            margin-bottom: 60px; /* Ruang untuk tanda tangan */
        }

        /* Seting Kertas Landscape saat Print */
        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Cetak Halaman</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h2>KARTU INVENTARIS BARANG (KIB) C</h2>
        <h3>GEDUNG DAN BANGUNAN</h3>
        <p>Lokasi: {{ ucfirst($lokasi) }} | Tanggal Cetak: {{ date('d-m-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kode Barang</th>
                <th rowspan="2">Nama Barang</th>
                <th rowspan="2">NIBAR</th>
                <th rowspan="2">No. Reg</th>
                <th rowspan="2">Spesifikasi Barang</th>
                <th rowspan="2">Lantai</th>
                <th colspan="2">Luas</th>
                <th rowspan="2">Lokasi / Alamat</th>
                <th rowspan="2">Status Tanah</th>
                <th rowspan="2">Tgl Perolehan</th>
                <th rowspan="2">Harga Satuan (Rp)</th>
                <th rowspan="2">Nilai Perolehan (Rp)</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>Jml</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataGedung as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->kode_barang }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->nbar }}</td>
                <td>{{ $item->nomor_register }}</td>
                <td>
                    {{ $item->spesifikasi_barang }}<br>
                    <small><i>{{ $item->spesifikasi_lainnya }}</i></small>
                </td>
                <td class="text-center">{{ $item->jumlah_lantai }}</td>
                <td class="text-center">{{ $item->jumlah }}</td>
                <td>{{ $item->satuan }}</td>
                <td>{{ $item->Lok }}</td>
                <td>{{ $item->status_kepemilikan_tanah }}</td>
                <td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '-' }}</td>
                <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                <td>{{ $item->keterangan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="15" class="text-center">Data tidak tersedia.</td>
            </tr>
            @endforelse
            {{-- Baris Total --}}
            @if(count($dataGedung) > 0)
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="13" class="text-center">TOTAL NILAI ASET</td>
                <td class="text-right">{{ number_format($dataGedung->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- Kolom Tanda Tangan --}}
    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,<br>Kepala SKPD/Unit Kerja</p>
            <br>
            <p><strong>(.......................................)</strong><br>NIP. ..................................</p>
        </div>
        <div class="signature-box">
            <p>Tasikmalaya, {{ date('d F Y') }}<br>Pengurus Barang</p>
            <br>
            <p><strong>(.......................................)</strong><br>NIP. ..................................</p>
        </div>
    </div>

</body>
</html>