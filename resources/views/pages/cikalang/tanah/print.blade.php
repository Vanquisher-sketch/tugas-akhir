<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Inventaris Tanah - {{ ucfirst($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @page {
            size: landscape; /* Agar kertas otomatis Landscape */
            margin: 10mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9pt; /* Font diperkecil sedikit agar muat */
        }
        .table th, .table td {
            padding: 0.2rem;
            vertical-align: middle;
            border: 1px solid #000 !important;
        }
        .header-table td {
            border: none !important;
            padding: 0;
            font-size: 10pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Styling khusus cetak */
        @media print {
            .no-print { display: none; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container-fluid">
        <h5 class="text-center font-weight-bold mb-1">DAFTAR BMD PADA KUASA PENGGUNA BARANG</h5>
        <h5 class="text-center font-weight-bold mb-4">TANAH (KIB A)</h5>
        
        <table class="table header-table mb-3" style="width: 60%;">
             <tbody>
                <tr>
                    <td width="200"><b>Kuasa Pengguna Barang</b></td>
                    <td>: ............................................</td>
                </tr>
                <tr>
                    <td><b>Kode Lokasi</b></td>
                    <td>: ............................................</td>
                </tr>
                 <tr>
                    <td><b>Kabupaten/Kota</b></td>
                    <td>: TASIKMALAYA</td> 
                </tr>
                <tr>
                    <td><b>Tahun</b></td>
                    <td>: {{ date('Y') }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Kode Barang</th>
                    <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">NIBAR</th>
                    <th rowspan="2">No. Register</th>
                    
                    {{-- REVISI: Ada Spesifikasi Barang --}}
                    <th rowspan="2">Spesifikasi Barang</th>
                    <th rowspan="2">Spesifikasi Lainnya</th>
                    
                    <th rowspan="2">Jml</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Lokasi</th>
                    <th rowspan="2">Koordinat</th>
                    
                    {{-- REVISI: Bukti Kepemilikan 4 Kolom --}}
                    <th colspan="4">Bukti Kepemilikan</th>
                    
                    <th rowspan="2">Harga Satuan (Rp)</th>
                    <th rowspan="2">Nilai Perolehan (Rp)</th>
                    <th rowspan="2">Cara Perolehan</th>
                    <th rowspan="2">Tgl Perolehan</th>
                    <th rowspan="2">Status</th>
                    <th rowspan="2">Ket</th>
                </tr>
                <tr>
                    {{-- Sub Header Bukti --}}
                    <th>Nama</th>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Kepemilikan</th>
                </tr>
                <tr style="font-size: 8pt; background-color: #f0f0f0;">
                    @for ($i = 1; $i <= 21; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse ($dataTanah as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->nibar }}</td>
                        <td>{{ $item->nomor_register }}</td>
                        
                        {{-- Data Spesifikasi --}}
                        <td>{{ $item->spesifikasi_barang }}</td>
                        <td>{{ $item->spesifikasi_lainnya }}</td>
                        
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td>{{ $item->satuan }}</td>
                        
                        {{-- REVISI: Pakai 'Lok' (L besar) --}}
                        <td>{{ $item->Lok }}</td>
                        
                        <td>{{ $item->titik_koordinat }}</td>
                        
                        {{-- REVISI: Data Bukti (4 Kolom) --}}
                        <td>{{ $item->bukti_nama }}</td>
                        <td>{{ $item->bukti_nomor }}</td>
                        <td class="text-center">{{ $item->bukti_tanggal ? \Carbon\Carbon::parse($item->bukti_tanggal)->format('d-m-Y') : '-' }}</td>
                        <td>{{ $item->nama_kepemilikan_dokumen }}</td>
                        
                        <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                        <td>{{ $item->cara_perolehan }}</td>
                        <td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '-' }}</td>
                        
                        {{-- REVISI: Tanggal Penggunaan DIHAPUS --}}
                        
                        <td>{{ $item->status_penggunaan }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="21" class="text-center font-italic">Data tidak tersedia.</td>
                    </tr>
                @endforelse
                
                @if($dataTanah->count() > 0)
                <tr class="font-weight-bold" style="background-color: #f0f0f0;">
                    <td colspan="15" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($dataTanah->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                    <td colspan="5"></td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Tasikmalaya, {{ date('d F Y') }}</p>
                <p>Kuasa Pengguna Barang</p>
                <br><br><br>
                <p class="font-weight-bold"><u>............................................</u></p>
                <p>NIP. ............................................</p>
            </div>
        </div>
    </div>
</body>
</html>