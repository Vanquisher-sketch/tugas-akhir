<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Monitoring Pajak - {{ ucfirst($lokasi) }}</title>
    <style>
        /* Pengaturan Kertas Landscape */
        @page {
            size: A4 landscape;
            margin: 10mm 15mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #000;
        }

        /* Kop Surat Sederhana */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header h2, .header h3, .header p {
            margin: 0;
            padding: 0;
        }
        .header h2 { font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .header h3 { font-size: 14pt; font-weight: bold; }
        .header p { font-size: 11pt; font-style: italic; }

        /* Tabel Data */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11pt;
        }
        
        table th, table td {
            border: 1px solid #000;
            padding: 6px 4px;
            vertical-align: middle;
        }

        table th {
            background-color: #f0f0f0; /* Abu-abu tipis untuk header */
            font-weight: bold;
            text-align: center;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        
        /* Status Text (Pengganti Badge Warna) */
        .status-telat { font-weight: bold; text-decoration: underline; }
        .status-dekat { font-weight: bold; font-style: italic; }

        /* Tanda Tangan */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            width: 300px;
            text-align: center;
        }
        .signature-box p { margin-bottom: 60px; } /* Jarak tanda tangan */

        /* Hilangkan elemen browser saat print */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer;">
            &larr; Kembali
        </button>
        <span style="margin-left: 10px; font-family: sans-serif; font-size: 10pt; color: gray;">
            *Halaman ini akan otomatis membuka dialog print.
        </span>
    </div>

    <div class="header">
        <h2>PEMERINTAH KABUPATEN TASIKMALAYA</h2>
        <h3>KECAMATAN {{ strtoupper($lokasi) }}</h3>
        <p>Laporan Monitoring Pajak Kendaraan Dinas & Operasional</p>
    </div>

    <p style="margin-bottom: 10px;">
        <strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} <br>
        <strong>Total Aset:</strong> {{ $pajaks->count() }} Unit
    </p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Nama Aset</th>
                <th width="12%">Nopol / Merk</th>
                <th width="15%">Pemakai</th>
                <th width="12%">Lokasi</th>
                <th width="13%">Pajak Tahunan</th>
                <th width="13%">Ganti Kaleng (5 Thn)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pajaks as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                
                <td>{{ $item->peralatan->nama_barang ?? '-' }}</td>
                
                <td class="text-center">
                    {{ $item->peralatan->nomor_polisi ?? $item->peralatan->merk_tipe ?? '-' }}
                </td>
                
                <td>
                    {{ $item->pemakai_nama }} <br>
                    <span style="font-size: 9pt;">({{ $item->nomor_pemakai ?? '-' }})</span>
                </td>
                
                <td class="text-center">{{ $item->alamat_penggunaan }}</td>

                <td class="text-center">
                    @if($item->tanggal_pajak)
                        @php
                            $tgl = \Carbon\Carbon::parse($item->tanggal_pajak);
                            $isLate = $tgl->isPast();
                            $isNear = $tgl->diffInDays(now()) < 30 && !$isLate;
                        @endphp
                        {{ $tgl->format('d/m/Y') }}
                        @if($isLate) <br><span class="status-telat">[LEWAT]</span>
                        @elseif($isNear) <br><span class="status-dekat">[SEGERA]</span>
                        @endif
                    @else
                        -
                    @endif
                </td>

                <td class="text-center">
                    @if($item->tanggal_stnk)
                        @php
                            $tglS = \Carbon\Carbon::parse($item->tanggal_stnk);
                            $isLateS = $tglS->isPast();
                        @endphp
                        {{ $tglS->format('d/m/Y') }}
                        @if($isLateS) <br><span class="status-telat">[LEWAT]</span> @endif
                    @else
                        -
                    @endif
                </td>

                <td style="font-size: 9pt;">
                    BAST: {{ $item->bast_file ? 'Ada' : 'Tdk' }} <br>
                    Dok: {{ $item->dokumen_lain_file ? 'Ada' : 'Tdk' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">Data tidak tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>
                Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Mengetahui,<br>
                <strong>Camat / Pejabat Penatausahaan</strong>
            </p>
            <br>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">
                ( ........................................... )
            </p>
            <span>NIP. ....................................</span>
        </div>
    </div>

</body>
</html>