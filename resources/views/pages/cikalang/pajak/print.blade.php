<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Monitoring Pajak - {{ strtoupper($lokasi) }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt; /* Sedikit dikecilkan agar muat banyak data */
            color: #000;
            line-height: 1.3;
        }

        /* Kop Surat Resmi */
        .header {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
        }
        .header h2 { font-size: 16pt; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .header h3 { font-size: 14pt; margin: 5px 0; text-transform: uppercase; }
        .header p { font-size: 10pt; margin: 0; font-style: italic; }
        .line-double { border-bottom: 3px solid #000; border-top: 1px solid #000; height: 2px; margin-top: 5px; margin-bottom: 20px; }

        /* Tabel Laporan */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Menjaga lebar kolom tetap konsisten */
        }
        
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            word-wrap: break-word;
        }

        table th {
            background-color: #e9e9e9 !important;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Penanda Status yang Jelas saat di-Print Hitam Putih */
        .status-danger { background-color: #ffcccc !important; color: #b30000; font-weight: bold; }
        .status-warning { background-color: #fff4cc !important; font-style: italic; }

        /* Area Tanda Tangan */
        .footer-section {
            margin-top: 30px;
            width: 100%;
        }
        .sig-table {
            border: none !important;
            width: 100%;
        }
        .sig-table td {
            border: none !important;
            width: 50%;
            text-align: center;
        }

        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; } /* Menjaga background-color tabel saat print */
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="background: #f8f9fa; padding: 10px; border-bottom: 1px solid #ddd; margin-bottom: 20px;">
        <button onclick="window.history.back()" style="padding: 5px 15px; cursor: pointer;">&larr; Kembali</button>
        <button onclick="window.print()" style="padding: 5px 15px; cursor: pointer; font-weight: bold;">Cetak Laporan</button>
    </div>

    <div class="header">
        <h2>PEMERINTAH KABUPATEN TASIKMALAYA</h2>
        <h3>KECAMATAN {{ strtoupper($lokasi) }}</h3>
        <p>Alamat: Jl. Raya {{ ucfirst($lokasi) }} No. 01 Tasikmalaya - Jawa Barat</p>
        <div class="line-double"></div>
        <h4 style="margin: 0; text-decoration: underline;">LAPORAN MONITORING PAJAK KENDARAAN</h4>
        <small>Per Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</small>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 120px;">Identitas Aset</th>
                <th style="width: 100px;">No. Polisi / Merk</th>
                <th style="width: 130px;">Nama Pemakai</th>
                <th style="width: 100px;">Alamat Unit</th>
                <th style="width: 90px;">Jatuh Tempo Pajak</th>
                <th style="width: 90px;">STNK (5 Thn)</th>
                <th>Keterangan Dokumen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pajaks as $item)
            @php
                $tglPajak = $item->tanggal_pajak ? \Carbon\Carbon::parse($item->tanggal_pajak) : null;
                $isLate = $tglPajak ? $tglPajak->isPast() : false;
                $isNear = $tglPajak ? ($tglPajak->diffInDays(now()) < 30 && !$isLate) : false;
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->peralatan->nama_barang ?? '-' }}</td>
                <td class="text-center">{{ $item->peralatan->nomor_polisi ?? '-' }} <br> <small>{{ $item->peralatan->merk_tipe ?? '-' }}</small></td>
                <td>
                    <strong>{{ $item->pemakai_nama }}</strong><br>
                    <small>HP: {{ $item->nomor_pemakai ?? '-' }}</small>
                </td>
                <td class="text-center">{{ $item->alamat_penggunaan }}</td>
                <td class="text-center {{ $isLate ? 'status-danger' : ($isNear ? 'status-warning' : '') }}">
                    {{ $tglPajak ? $tglPajak->format('d/m/Y') : '-' }}
                    @if($isLate) <br><small>(TERLAMBAT)</small>
                    @elseif($isNear) <br><small>(SEGERA)</small> @endif
                </td>
                <td class="text-center {{ ($item->tanggal_stnk && \Carbon\Carbon::parse($item->tanggal_stnk)->isPast()) ? 'status-danger' : '' }}">
                    {{ $item->tanggal_stnk ? \Carbon\Carbon::parse($item->tanggal_stnk)->format('d/m/Y') : '-' }}
                </td>
                <td style="font-size: 8pt;">
                    - BAST: {{ $item->bast_nomor ?? 'Tidak Ada' }}<br>
                    - DOK LAIN: {{ $item->dokumen_lain_nama ?? '-' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">Data Tidak Ditemukan</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-section">
        <table class="sig-table">
            <tr>
                <td></td>
                <td>
                    Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Mengetahui,<br>
                    <strong>CAMAT {{ strtoupper($lokasi) }}</strong>
                    <br><br><br><br>
                    <span style="text-decoration: underline; font-weight: bold;">( ........................................... )</span><br>
                    NIP. ........................................
                </td>
            </tr>
        </table>
    </div>

</body>
</html>