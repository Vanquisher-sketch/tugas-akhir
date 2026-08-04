<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KIB A - {{ ucfirst($lokasi) }}</title>
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
        {{-- HEADER KIB A --}}
        <h6 class="text-center font-weight-bold text-uppercase mb-0">KARTU INVENTARIS BARANG (KIB) A</h6>
        <h6 class="text-center font-weight-bold mb-4">TANAH - {{ strtoupper($lokasi == 'tawang' ? 'KECAMATAN TAWANG' : 'KELURAHAN ' . $lokasi) }}</h6>
        
        {{-- TABEL DATA KIB A --}}
        <table class="table table-bordered">
            <thead class="text-center">
                <tr class="font-weight-bold">
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
                <tr class="font-weight-bold">
                    <th>Dokumen</th>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Nama Pemilik</th>
                </tr>
            </thead>
            <tbody>
                @php $totalHarga = 0; @endphp
                @forelse ($dataTanah as $index => $item)
                    @php
                        // ✅ Hitung Manual Harga untuk mencegah error jika nama atribut berubah
                        $harga = $item->nilai_perolehan ?? $item->bmd_nilai_perolehan ?? $item->bmd_harga ?? 0;
                        $totalHarga += $harga;
                        
                        // ✅ Tangkap Tanggal dengan aman
                        $tglBukti = $item->bukti_tanggal ?? $item->tanah_tanggal_bukti ?? null;
                        $tglPerolehan = $item->tanggal_perolehan ?? $item->bmd_tanggal_perolehan ?? $item->bmd_tahun ?? null;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->kode_barang ?? $item->bmd_kode_barang ?? '-' }}</td>
                        <td>{{ $item->nama_barang ?? $item->bmd_nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $item->nomor_register ?? $item->bmd_register ?? '-' }}</td>
                        
                        {{-- Luas M2 --}}
                        <td class="text-center font-weight-bold">
                            {{ number_format($item->jumlah ?? $item->tanah_luas ?? $item->bmd_jumlah ?? 0, 0, ',', '.') }}
                        </td>
                        
                        <td>{{ $item->Lok ?? $item->lokasi ?? $item->tanah_alamat ?? '-' }}</td>
                        
                        {{-- Bukti Kepemilikan --}}
                        <td class="text-center">{{ $item->bukti_nama ?? $item->tanah_hak ?? $item->bukti_dokumen ?? '-' }}</td>
                        <td>{{ $item->bukti_nomor ?? $item->tanah_sertifikat_nomor ?? '-' }}</td>
                        <td class="text-center">{{ $tglBukti ? \Carbon\Carbon::parse($tglBukti)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->nama_kepemilikan_dokumen ?? $item->tanah_penggunaan ?? '-' }}</td>
                        
                        {{-- Nilai Aset --}}
                        <td class="text-right font-weight-bold">{{ number_format($harga, 0, ',', '.') }}</td>
                        
                        <td>{{ $item->cara_perolehan ?? $item->bmd_asal_usul ?? $item->bmd_cara_perolehan ?? '-' }}</td>
                        <td class="text-center">{{ $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->format('Y') : '-' }}</td>
                        <td>{{ $item->keterangan ?? $item->bmd_keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center py-4 font-italic text-muted">
                            Belum ada data inventaris KIB A (Tanah) pada lokasi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="font-weight-bold bg-light">
                <tr>
                    <td colspan="10" class="text-right text-uppercase">Total Nilai Kapitalisasi Aset (KIB A)</td>
                    {{-- ✅ Menggunakan variabel akumulasi manual yang 100% akurat --}}
                    <td class="text-right text-primary">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
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