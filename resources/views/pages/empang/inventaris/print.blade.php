<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KIB Ruangan - {{ $room->name }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 9pt; color: #000; }
        
        .table th, .table td { 
            padding: 0.3rem; 
            vertical-align: middle; 
            border: 1px solid #000 !important; 
        }
        .header-table td { border: none !important; padding: 0.1rem 0; }
        .text-center { text-align: center; }
        .font-weight-bold { font-weight: bold; }
        
        /* PENGATURAN KHUSUS UNTUK PDF / PRINT */
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; background-color: #f2f2f2 !important; }
        tfoot { display: table-footer-group; }
        
        .catatan { font-size: 8pt; margin-top: 10px; line-height: 1.2; page-break-inside: avoid; }
        .signature-table { margin-top: 30px; width: 100%; page-break-inside: avoid; border-collapse: collapse; border: none; }
        .signature-table td { border: none !important; padding: 0; }
        
        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container-fluid">
        {{-- HEADER KIR --}}
        <div class="text-center mb-3">
            <h5 class="font-weight-bold mb-0">KARTU INVENTARIS RUANGAN (KIR)</h5>
            <h6 class="mb-0">PROVINSI JAWA BARAT - KOTA TASIKMALAYA</h6>
            <h6 class="mb-0">TAHUN ANGGARAN {{ date('Y') }}</h6>
        </div>

        {{-- IDENTITAS RUANGAN --}}
        <table class="table header-table mb-2" style="width: 60%;">
             <tbody>
                <tr>
                    <td style="width: 25%;"><b>SKPD / OPD</b></td>
                    <td>: KECAMATAN TAWANG</td>
                </tr>
                <tr>
                    <td><b>UNIT KERJA / LOKASI</b></td>
                    <td>: {{ strtoupper($lokasi == 'tawang' ? 'KECAMATAN TAWANG' : 'KELURAHAN ' . $lokasi) }}</td>
                </tr>
                 <tr>
                    <td><b>RUANGAN / KODE</b></td>
                    <td>: {{ strtoupper($room->name) }} / <strong>{{ $room->kode_ruangan }}</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- TABEL DATA BARANG --}}
        <table class="table table-bordered">
            <thead class="text-center">
                <tr class="font-weight-bold">
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">NIBAR</th>
                    <th rowspan="2" class="align-middle">No. Reg</th>
                    <th rowspan="2" class="align-middle">Kode Barang</th>
                    <th rowspan="2" class="align-middle">Nama Barang</th>
                    <th rowspan="2" class="align-middle">Spesifikasi</th>
                    <th colspan="2">Detail Barang</th>
                    <th rowspan="2" class="align-middle">Jumlah</th>
                    <th rowspan="2" class="align-middle">Satuan</th>
                    <th rowspan="2" class="align-middle">Ket.</th>
                </tr>
                <tr class="font-weight-bold">
                    <th>Merek/Tipe</th>
                    <th>Tahun</th>
                </tr>
                <tr style="background-color: #eee;">
                    @for ($i = 1; $i <= 11; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse ($dataInventaris as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    
                    {{-- IMPLEMENTASI SABUK PENGAMAN ATRIBUT (??) --}}
                    <td>{{ $item->nibar ?? $item->bmd_nibar ?? '-' }}</td>
                    <td>{{ $item->nomor_register ?? $item->bmd_register ?? '-' }}</td>
                    
                    <td class="text-center font-weight-bold">
                        {{ $item->kode_barang ?? $item->alat_kode_barang ?? '-' }}
                    </td>
                    
                    <td>{{ $item->nama_barang ?? $item->alat_nama_barang ?? '-' }}</td>
                    <td>{{ $item->spesifikasi_barang ?? $item->bmd_spesifikasi ?? '-' }}</td>
                    <td>{{ $item->merk_tipe ?? $item->alat_merk_tipe ?? '-' }}</td>
                    
                    <td class="text-center">{{ $item->tahun_perolehan ?? $item->bmd_tahun ?? '-' }}</td>
                    <td class="text-center font-weight-bold">{{ $item->jumlah ?? $item->bmd_jumlah ?? '1' }}</td>
                    <td>{{ $item->satuan ?? $item->bmd_satuan ?? '-' }}</td>
                    <td>{{ $item->keterangan ?? $item->bmd_keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-4 font-italic text-muted">
                        Belum ada data inventaris pada ruangan ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- CATATAN KAKI --}}
        <div class="catatan">
            <b>Catatan :</b><br>
            <i>Tidak dibenarkan memindahkan barang-barang yang ada pada daftar barang ini tanpa sepengetahuan pengurus barang pengguna / pembantu dan penanggung jawab ruangan.</i>
        </div>
        
        {{-- AREA TANDA TANGAN (Table Mode untuk keamanan cetak PDF) --}}
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
                    <p>Penanggung Jawab Ruangan,</p>
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
            <i class="fas fa-print mr-1"></i> Cetak Dokumen
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow-sm">
            Tutup
        </button>
    </div>
    
</body>
</html>