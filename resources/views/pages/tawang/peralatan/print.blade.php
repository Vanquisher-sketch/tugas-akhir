<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak KIB B - {{ ucfirst($lokasi) }}</title>
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
        {{-- HEADER KIB B --}}
        <h6 class="text-center font-weight-bold mb-0">DAFTAR INVENTARIS BARANG MILIK DAERAH (BMD)</h6>
        <h6 class="text-center font-weight-bold mb-4">PERALATAN DAN MESIN (KIB B)</h6>
        
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

        {{-- TABEL DATA KIB B --}}
        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr class="font-weight-bold">
                    <th rowspan="2">No</th>
                    <th rowspan="2">Kode Barang</th>
                    <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">Register</th>
                    <th colspan="2">Spesifikasi</th>
                    <th colspan="3">Nomor Kendaraan</th>
                    <th rowspan="2">Jml</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Harga Perolehan (Rp)</th>
                    <th rowspan="2">Tgl Perolehan</th>
                    <th rowspan="2">Keterangan</th>
                </tr>
                <tr class="font-weight-bold">
                    <th>Merk/Tipe</th>
                    <th>Lainnya</th>
                    <th>Polisi</th>
                    <th>Rangka</th>
                    <th>BPKB</th>
                </tr>
            </thead>
            <tbody>
                @php $totalHarga = 0; @endphp
                @forelse ($dataPeralatan as $index => $item)
                    @php
                        // ✅ Hitung Manual Harga mengambil langsung dari tabel Peralatan
                        $harga = $item->alat_nilai_perolehan ?? 0;
                        $totalHarga += $harga;
                        
                        // ✅ Tangkap Tanggal dengan aman
                        $tglPerolehan = $item->alat_tanggal_perolehan ?? null;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        
                        {{-- Identitas Barang --}}
                        <td class="text-center">{{ $item->alat_kode_barang ?? '-' }}</td>
                        <td>{{ $item->alat_nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $item->alat_nomor_register ?? '-' }}</td>
                        
                        {{-- Spesifikasi Umum --}}
                        <td>{{ $item->alat_merk_tipe ?? '-' }}</td>
                        <td>{{ $item->alat_spesifikasi_lainnya ?? '-' }}</td>
                        
                        {{-- Spesifikasi Kendaraan --}}
                        <td class="text-center font-weight-bold">{{ $item->alat_nomor_polisi ?? '-' }}</td>
                        <td><small>{{ $item->alat_nomor_rangka ?? '-' }}</small></td>
                        <td>{{ $item->alat_nomor_bpkb ?? '-' }}</td>
                        
                        {{-- Kuantitas & Harga --}}
                        <td class="text-center font-weight-bold">{{ $item->alat_jumlah ?? '1' }}</td>
                        <td class="text-center">{{ $item->alat_satuan ?? '-' }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($harga, 0, ',', '.') }}</td>
                        
                        {{-- Waktu & Keterangan --}}
                        <td class="text-center">{{ $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->alat_keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center py-4 font-italic text-muted">
                            Belum ada data inventaris KIB B (Peralatan dan Mesin) pada lokasi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="font-weight-bold bg-light">
                <tr>
                    {{-- ✅ Colspan diubah menjadi 11 agar total harga sejajar dengan kolom Harga Perolehan --}}
                    <td colspan="11" class="text-right text-uppercase">Total Nilai Kapitalisasi Aset (KIB B)</td>
                    <td class="text-right text-dark">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        {{-- AREA TANDA TANGAN (Menggunakan Table agar tidak berantakan saat cetak PDF) --}}
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
            <i class="fas fa-print mr-1"></i> Cetak Dokumen KIB B
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow-sm">
            Tutup
        </button>
    </div>
</body>
</html>