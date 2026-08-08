<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak KIB A - {{ ucfirst($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* FONT DIPERKECIL JADI 6.5pt AGAR 20 KOLOM MUAT DI KERTAS A4 */
        body { font-family: 'Times New Roman', serif; font-size: 6.5pt; color: #000; }
        
        .table-bordered th, .table-bordered td { 
            border: 1px solid #000 !important; 
            vertical-align: middle; 
            padding: 2px 3px !important; /* Padding diperketat */
            word-wrap: break-word;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* PENGATURAN KHUSUS UNTUK PDF / PRINT */
        @page { size: A4 landscape; margin: 10mm; }
        table { page-break-inside: auto; width: 100%; table-layout: fixed; }
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
        {{-- HEADER KIB A --}}
        <h6 class="text-center font-weight-bold text-uppercase mb-0" style="font-size: 10pt;">DAFTAR INVENTARIS BARANG MILIK DAERAH (BMD)</h6>
        <h6 class="text-center font-weight-bold mb-4" style="font-size: 10pt;">KARTU INVENTARIS BARANG (KIB) A - TANAH</h6>
        
        <table class="mb-2" style="width: 40%; font-weight: bold; font-size: 8pt; border: none;">
            <tr>
                <td width="120" style="border: none; padding: 0;">LOKASI</td>
                <td style="border: none; padding: 0;">: {{ strtoupper($lokasi == 'tawang' ? 'KECAMATAN TAWANG' : 'KELURAHAN ' . $lokasi) }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 0;">KABUPATEN/KOTA</td>
                <td style="border: none; padding: 0;">: TASIKMALAYA</td>
            </tr>
            <tr>
                <td style="border: none; padding: 0;">TAHUN ANGGARAN</td>
                <td style="border: none; padding: 0;">: {{ date('Y') }}</td>
            </tr>
        </table>
        
        {{-- TABEL DATA KIB A (20 KOLOM) --}}
        <table class="table table-bordered">
            <thead class="text-center">
                <tr class="font-weight-bold bg-light">
                    <th rowspan="2" style="width: 2%;">No</th>
                    <th rowspan="2" style="width: 5%;">Kode Barang</th>
                    <th rowspan="2" style="width: 3%;">No. Reg</th>
                    <th rowspan="2" style="width: 6%;">Nama Barang</th>
                    <th colspan="2">Spesifikasi</th>
                    <th rowspan="2" style="width: 4%;">Luas</th>
                    <th rowspan="2" style="width: 3%;">Satuan</th>
                    <th rowspan="2" style="width: 6%;">Alamat Fisik</th>
                    <th rowspan="2" style="width: 5%;">Koordinat</th>
                    <th colspan="4">Bukti Dokumen</th>
                    <th rowspan="2" style="width: 6%;">Harga Satuan (Rp)</th>
                    <th rowspan="2" style="width: 7%;">Nilai Perolehan (Rp)</th>
                    <th rowspan="2" style="width: 5%;">Cara Perolehan</th>
                    <th rowspan="2" style="width: 4%;">Tanggal Perolehan</th>
                    <th rowspan="2" style="width: 4%;">Status Penggunaan</th>
                    <th rowspan="2" style="width: 6%;">Keterangan</th>
                </tr>
                <tr class="font-weight-bold bg-light">
                    <th style="width: 5%;">Barang</th>
                    <th style="width: 5%;">Lainnya</th>
                    <th style="width: 4%;">Nama</th>
                    <th style="width: 4%;">Nomor</th>
                    <th style="width: 4%;">Tanggal</th>
                    <th style="width: 5%;">Pemilik</th>
                </tr>
            </thead>
            <tbody>
                @php $totalHarga = 0; @endphp
                @forelse ($dataTanah as $index => $item)
                    @php
                        // ✅ Hitung Nilai Perolehan
                        $harga = $item->tanah_nilai_perolehan ?? 0;
                        $totalHarga += $harga;
                        
                        // ✅ Tangkap Tanggal
                        $tglBukti = $item->tanah_bukti_tanggal ?? null;
                        $tglPerolehan = $item->tanah_tanggal_perolehan ?? null;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        
                        <td class="text-center font-weight-bold">{{ $item->tanah_kode_barang ?? '-' }}</td>
                        <td class="text-center">{{ $item->tanah_nomor_register ?? '-' }}</td>
                        <td>{{ $item->tanah_nama_barang ?? '-' }}</td>
                        
                        {{-- Spesifikasi --}}
                        <td>{{ $item->tanah_spesifikasi_barang ?? '-' }}</td>
                        <td>{{ $item->tanah_spesifikasi_lainnya ?? '-' }}</td>
                        
                        {{-- Luas & Satuan --}}
                        <td class="text-center font-weight-bold">{{ number_format($item->tanah_jumlah ?? 0, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $item->tanah_satuan ?? '-' }}</td>
                        
                        {{-- Lokasi & Koordinat --}}
                        <td>{{ $item->tanah_lokasi_fisik ?? '-' }}</td>
                        <td class="text-center">{{ $item->tanah_titik_koordinat ?? '-' }}</td>
                        
                        {{-- Bukti Kepemilikan (4 Kolom) --}}
                        <td class="text-center">{{ $item->tanah_bukti_nama ?? '-' }}</td>
                        <td>{{ $item->tanah_bukti_nomor ?? '-' }}</td>
                        <td class="text-center">{{ $tglBukti ? \Carbon\Carbon::parse($tglBukti)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->tanah_nama_kepemilikan_dokumen ?? '-' }}</td>
                        
                        {{-- Nilai Aset (Satuan dan Perolehan) --}}
                        <td class="text-right">{{ $item->tanah_harga_satuan ? number_format($item->tanah_harga_satuan, 0, ',', '.') : '-' }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($harga, 0, ',', '.') }}</td>
                        
                        {{-- Informasi Tambahan --}}
                        <td class="text-center">{{ $item->tanah_cara_perolehan ?? '-' }}</td>
                        <td class="text-center">{{ $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">{{ $item->tanah_status_penggunaan ?? '-' }}</td>
                        <td>{{ $item->tanah_keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" class="text-center py-4 font-italic text-muted">
                            Belum ada data inventaris KIB A (Tanah) pada lokasi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="font-weight-bold bg-light">
                <tr>
                    {{-- Colspan 15 agar total nilai sejajar pas di bawah kolom Nilai Perolehan --}}
                    <td colspan="15" class="text-right text-uppercase">Total Nilai Kapitalisasi Aset (KIB A)</td>
                    <td class="text-right text-primary">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>

        {{-- AREA TANDA TANGAN KIB --}}
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
                    <p>Pengurus Barang,</p>
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
            <i class="fas fa-print mr-1"></i> Cetak Dokumen KIB A
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow-sm">
            Tutup
        </button>
    </div>
</body>
</html>