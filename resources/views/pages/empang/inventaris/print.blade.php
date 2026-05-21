<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KIB Ruangan - {{ $room->name }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        
        .signature-block { margin-top: 30px; page-break-inside: avoid; }
        .signature-block .signer { margin-top: 50px; }
        
        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
        
        .catatan { font-size: 8pt; margin-top: 10px; line-height: 1.2; }
        thead { background-color: #f2f2f2 !important; }
    </style>
</head>
<body onload="window.print()">

    <div class="container-fluid">
        <div class="text-center mb-3">
            <h5 class="font-weight-bold mb-0">KARTU INVENTARIS RUANGAN (KIR)</h5>
            <h6 class="mb-0">PROVINSI JAWA BARAT - KOTA TASIKMALAYA</h6>
            <h6 class="mb-0">TAHUN ANGGARAN {{ date('Y') }}</h6>
        </div>

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
                    {{-- REVISI: Menampilkan Nama dan Kode Ruangan sebagai identitas utama --}}
                    <td>: {{ strtoupper($room->name) }} / <strong>{{ $room->kode_ruangan }}</strong></td>
                </tr>
            </tbody>
        </table>

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
                    <td>{{ $item->nibar }}</td>
                    <td>{{ $item->nomor_register }}</td>
                    {{-- Kode Barang sebagai Primary Key --}}
                    <td class="text-center font-weight-bold">{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->spesifikasi_barang }}</td>
                    <td>{{ $item->merk_tipe }}</td>
                    <td class="text-center">{{ $item->tahun_perolehan }}</td>
                    <td class="text-center font-weight-bold">{{ $item->jumlah }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center">Belum ada data inventaris pada ruangan ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="catatan">
            <b>Catatan :</b><br>
            <i>Tidak dibenarkan memindahkan barang-barang yang ada pada daftar barang ini tanpa sepengetahuan pengurus barang pengguna / pembantu dan penanggung jawab ruangan.</i>
        </div>
        
        <div class="row signature-block">
            <div class="col-4 text-center">
                <p class="mb-0">Mengetahui,</p>
                <p>Kepala {{ $lokasi == 'tawang' ? 'Kecamatan Tawang' : 'Kelurahan ' . ucfirst($lokasi) }}</p>
                <div class="signer">
                    <p class="font-weight-bold mb-0"><u>............................................</u></p>
                    <p>NIP. ............................................</p>
                </div>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <p class="mb-0">Tasikmalaya, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p>Penanggung Jawab Ruangan,</p>
                <div class="signer">
                    <p class="font-weight-bold mb-0"><u>............................................</u></p>
                    <p>NIP. ............................................</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary">Cetak Dokumen</button>
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
    </div>
</body>
</html>