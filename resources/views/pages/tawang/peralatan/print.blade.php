<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak KIB B - {{ ucfirst($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* FONT DIPERKECIL JADI 6.5pt AGAR 21 KOLOM MUAT DI A4 */
        body { font-family: 'Times New Roman', serif; font-size: 6.5pt; color: #000; }
        
        .table-bordered th, .table-bordered td { 
            border: 1px solid #000 !important; 
            vertical-align: middle; 
            padding: 2px 3px !important; 
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
        {{-- HEADER KIB B --}}
        <h6 class="text-center font-weight-bold mb-0" style="font-size: 10pt;">DAFTAR INVENTARIS BARANG MILIK DAERAH (BMD)</h6>
        <h6 class="text-center font-weight-bold mb-4" style="font-size: 10pt;">KARTU INVENTARIS BARANG (KIB) B - PERALATAN DAN MESIN</h6>
        
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

        {{-- TABEL DATA KIB B (21 KOLOM DENGAN STNK & PAJAK) --}}
        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr class="font-weight-bold">
                    <th rowspan="2" style="width: 2%;">No</th>
                    <th rowspan="2" style="width: 4%;">Kode Barang</th>
                    <th rowspan="2" style="width: 6%;">Nama Barang</th>
                    <th rowspan="2" style="width: 3%;">No. Reg</th>
                    <th colspan="3">Spesifikasi</th>
                    <th colspan="5">Identitas Kendaraan</th>
                    <th colspan="2">Kuantitas</th>
                    <th colspan="2">Nilai Aset (Rp)</th>
                    <th rowspan="2" style="width: 4%;">Cara Perolehan</th>
                    <th rowspan="2" style="width: 4%;">Tanggal Perolehan</th>
                    <th rowspan="2" style="width: 3%;">Kondisi</th>
                    <th rowspan="2" style="width: 3%;">Status</th>
                    <th rowspan="2" style="width: 4%;">Ket</th>
                </tr>
                <tr class="font-weight-bold">
                    <th style="width: 4%;">Merk/Tipe</th>
                    <th style="width: 4%;">Ukuran</th>
                    <th style="width: 4%;">Lainnya</th>
                    <th style="width: 4%;">Polisi</th>
                    <th style="width: 4%;">Rangka</th>
                    <th style="width: 4%;">BPKB</th>
                    <th style="width: 4%;">STNK</th>
                    <th style="width: 4%;">Pajak</th>
                    <th style="width: 2%;">Jumlah</th>
                    <th style="width: 3%;">Satuan</th>
                    <th style="width: 5%;">Satuan</th>
                    <th style="width: 5%;">Total Perolehan</th>
                </tr>
            </thead>
            <tbody>
                @php $totalHarga = 0; @endphp
                @forelse ($dataPeralatan as $index => $item)
                    @php
                        $harga = $item->alat_nilai_perolehan ?? $item->nilai_perolehan ?? 0;
                        $totalHarga += $harga;
                        
                        $tglPerolehan = $item->alat_tanggal_perolehan ?? $item->tanggal_perolehan ?? null;
                        
                        // 🌟 FALLBACK PAJAK: Mencari semua kemungkinan nama kolom di database
                        $tglstnk = $item->alat_tanggal_stnk ?? $item->tanggal_stnk ?? $item->stnk ?? null; 
                        $tglPajak = $item->alat_tanggal_pajak ?? $item->tanggal_pajak ?? $item->pajak ?? null; 
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        
                        {{-- Identitas Utama --}}
                        <td class="text-center font-weight-bold">{{ $item->alat_kode_barang ?? $item->kode_barang ?? '-' }}</td>
                        <td>{{ $item->alat_nama_barang ?? $item->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $item->alat_nomor_register ?? $item->nomor_register ?? '-' }}</td>
                        
                        {{-- Spesifikasi Barang --}}
                        <td>{{ $item->alat_merk_tipe ?? $item->merk_tipe ?? '-' }}</td>
                        <td>{{ $item->alat_spesifikasi_barang ?? $item->spesifikasi_barang ?? '-' }}</td>
                        <td>{{ $item->alat_spesifikasi_lainnya ?? $item->spesifikasi_lainnya ?? '-' }}</td>
                        
                        {{-- Identitas Kendaraan --}}
                        <td class="text-center font-weight-bold">{{ $item->alat_nomor_polisi ?? $item->nomor_polisi ?? '-' }}</td>
                        <td class="text-center">{{ $item->alat_nomor_rangka ?? $item->nomor_rangka ?? '-' }}</td>
                        <td class="text-center">{{ $item->alat_nomor_bpkb ?? $item->nomor_bpkb ?? '-' }}</td>
                        
                        {{-- 🌟 FALLBACK STNK: Mencari semua kemungkinan nama kolom STNK --}}
                        <td class="text-center">{{ $tglstnk ? \Carbon\Carbon::parse($tglstnk)->format('d/m/Y') : '-' }}</td>
                        
                        <td class="text-center">{{ $tglPajak ? \Carbon\Carbon::parse($tglPajak)->format('d/m/Y') : '-' }}</td> 
                        
                        {{-- Kuantitas & Harga --}}
                        <td class="text-center font-weight-bold">{{ $item->alat_jumlah ?? $item->jumlah ?? '1' }}</td>
                        <td class="text-center">{{ $item->alat_satuan ?? $item->satuan ?? '-' }}</td>
                        <td class="text-right">{{ $item->alat_harga_satuan ?? $item->harga_satuan ? number_format($item->alat_harga_satuan ?? $item->harga_satuan, 0, ',', '.') : '-' }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($harga, 0, ',', '.') }}</td>
                        
                        {{-- Waktu, Status, Kondisi, Keterangan --}}
                        <td class="text-center">{{ $item->alat_cara_perolehan ?? $item->cara_perolehan ?? '-' }}</td>
                        <td class="text-center">{{ $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">{{ $item->alat_kondisi ?? $item->kondisi ?? '-' }}</td>
                        <td class="text-center">{{ $item->alat_status_penggunaan ?? $item->status_penggunaan ?? '-' }}</td>
                        <td>{{ $item->alat_keterangan ?? $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="21" class="text-center py-4 font-italic text-muted">
                            Belum ada data inventaris KIB B (Peralatan dan Mesin) pada lokasi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="font-weight-bold bg-light">
                <tr>
                    <td colspan="15" class="text-right text-uppercase">Total Nilai Kapitalisasi Aset (KIB B)</td>
                    <td class="text-right text-primary">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                    <td colspan="5"></td>
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
            <i class="fas fa-print mr-1"></i> Cetak Dokumen KIB B
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow-sm">
            Tutup
        </button>
    </div>
</body>
</html>