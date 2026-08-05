<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KIB C - {{ strtoupper($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 8pt; color: #000; }
        
        .table-bordered th, .table-bordered td { 
            border: 1px solid #000 !important; 
            vertical-align: middle; 
            padding: 4px; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header-info td { border: none !important; padding: 0; font-weight: bold; font-size: 9pt; }
        
        /* PENGATURAN KHUSUS UNTUK PDF / PRINT */
        @page { size: A4 landscape; margin: 10mm; }
        table { page-break-inside: auto; width: 100%; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; background-color: #f2f2f2 !important; }
        tfoot { display: table-footer-group; }
        
        .signature-table { margin-top: 30px; width: 100%; page-break-inside: avoid; border-collapse: collapse; border: none; }
        .signature-table td { border: none !important; padding: 0; font-size: 9pt; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        {{-- HEADER KIB C --}}
        <h6 class="text-center font-weight-bold mb-0">DAFTAR BMD PADA KUASA PENGGUNA BARANG</h6>
        <h6 class="text-center font-weight-bold mb-4">GEDUNG DAN BANGUNAN (KIB C)</h6>
        
        <table class="header-info mb-3" style="width: 40%;">
            <tr>
                <td width="150">LOKASI</td>
                <td>: {{ strtoupper($lokasi == 'tawang' ? 'KECAMATAN TAWANG' : 'KELURAHAN ' . $lokasi) }}</td>
            </tr>
            <tr>
                <td>KABUPATEN/KOTA</td>
                <td>: TASIKMALAYA</td>
            </tr>
            <tr>
                <td>TAHUN ANGGARAN</td>
                <td>: {{ date('Y') }}</td>
            </tr>
        </table>
        
        {{-- TABEL DATA KIB C --}}
        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Gedung</th>
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
                @php $totalHarga = 0; @endphp
                @forelse ($dataGedung as $index => $item)
                    @php
                        // Kalkulasi manual aman dari error DB
                        $harga = $item->nilai_perolehan ?? $item->bmd_nilai_perolehan ?? $item->gedung_harga ?? 0;
                        $totalHarga += $harga;
                        
                        // Parse tanggal aman
                        $tglPerolehan = $item->tanggal_perolehan ?? $item->bmd_tanggal_perolehan ?? $item->bmd_tahun ?? null;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->kode_barang ?? $item->bmd_kode_barang ?? '-' }}</td>
                        <td>{{ $item->nama_barang ?? $item->gedung_nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $item->nomor_register ?? $item->bmd_register ?? '-' }}</td>
                        
                        <td class="text-center">{{ $item->jumlah_lantai ?? $item->gedung_lantai ?? '-' }}</td>
                        <td class="text-center font-weight-bold">{{ number_format($item->jumlah ?? $item->gedung_luas ?? $item->bmd_jumlah ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $item->Lok ?? $item->lokasi ?? $item->gedung_alamat ?? '-' }}</td>
                        
                        <td class="text-center">{{ $item->status_kepemilikan_tanah ?? $item->gedung_status_tanah ?? '-' }}</td>
                        <td class="text-center">{{ $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->format('d/m/Y') : '-' }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($harga, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan ?? $item->bmd_keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-4 font-italic text-muted">
                            Belum ada data inventaris KIB C (Gedung dan Bangunan) pada lokasi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="font-weight-bold bg-light">
                <tr>
                    <td colspan="10" class="text-right text-uppercase">Total Nilai Kapitalisasi Aset (KIB C)</td>
                    <td class="text-right text-primary">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        {{-- AREA TANDA TANGAN --}}
        <table class="signature-table">
            <tr>
                <td style="width: 35%; text-align: center; vertical-align: top;">
                    <p class="mb-0">Mengetahui,</p>
                    <p>Kepala {{ $lokasi == 'tawang' ? 'Kecamatan Tawang' : 'Kelurahan ' . ucfirst($lokasi) }}</p>
                    <br><br><br><br>
                    <p class="font-weight-bold mb-0"><u>............................................</u></p>
                    <p>NIP. ............................................</p>
                </td>
                <td style="width: 30%;"></td>
                <td style="width: 35%; text-align: center; vertical-align: top;">
                    <p class="mb-0">Tasikmalaya, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                    <p>Kuasa Pengguna Barang,</p>
                    <br><br><br><br>
                    <p class="font-weight-bold mb-0"><u>............................................</u></p>
                    <p>NIP. ............................................</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- TOMBOL AKSI CETAK --}}
    <div class="text-center mt-4 mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary font-weight-bold shadow-sm mr-2">
            <i class="fas fa-print mr-1"></i> Cetak Dokumen KIB C
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow-sm">
            Tutup
        </button>
    </div>
</body>
</html>