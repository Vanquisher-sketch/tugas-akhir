<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Ruangan - {{ ucfirst($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; }
        .table-bordered th, .table-bordered td { border: 1px solid black !important; vertical-align: middle; }
        thead { background-color: #e9ecef; }
        @media print {
            @page { size: portrait; margin: 1cm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <div class="text-center mb-4">
            <h5 class="mb-0">DAFTAR RUANGAN</h5>
            <h6>SKPD: KECAMATAN {{ strtoupper($lokasi) }}</h6>
        </div>

        <table class="table table-bordered table-sm">
            <thead class="text-center">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Ruangan</th>
                    <th>Kode Ruangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataRuangan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->kode_ruangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Ruangan - {{ ucfirst($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Gaya Font Resmi untuk Dokumen Pemerintahan */
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12pt;
        }
        
        /* Mempertegas Garis Tabel saat di-print */
        .table-bordered th, .table-bordered td { 
            border: 1px solid black !important; 
            vertical-align: middle; 
            padding: 8px;
        }
        
        thead { 
            background-color: #f2f2f2 !important; 
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-box {
            border-bottom: 3px double black;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        @media print {
            @page { 
                size: portrait; 
                margin: 2cm; 
            }
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
            }
            /* Menghilangkan link URL di bagian bawah kertas saat di-print */
            a[href]:after { content: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container mt-3">
        {{-- KOP SURAT SEDERHANA --}}
        <div class="header-box text-center">
            <h5 class="mb-0">PEMERINTAH KOTA TASIKMALAYA</h5>
            <h5 class="mb-0">KECAMATAN TAWANG</h5>
            @if($lokasi !== 'tawang')
                <h6 class="mb-0">KELURAHAN {{ strtoupper($lokasi) }}</h6>
            @endif
            <hr style="margin-top: 5px; margin-bottom: 2px; border: 1px solid black;">
            <p class="mb-0">DAFTAR INVENTARIS RUANGAN</p>
        </div>

        <div class="mb-3">
            <table style="width: 100%; font-weight: bold;">
                <tr>
                    <td style="width: 15%;">LOKASI</td>
                    <td style="width: 2%;">:</td>
                    <td>{{ strtoupper($lokasi) }}</td>
                </tr>
                <tr>
                    <td>TANGGAL CETAK</td>
                    <td>:</td>
                    <td>{{ date('d-m-Y') }}</td>
                </tr>
            </table>
        </div>

        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th style="width: 8%;">NO</th>
                    <th style="width: 30%;">KODE RUANGAN</th>
                    <th>NAMA RUANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataRuangan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        {{-- Menampilkan Kode Ruangan yang sekarang jadi Primary Key --}}
                        <td class="text-center"><strong>{{ $item->kode_ruangan }}</strong></td>
                        <td>{{ $item->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center italic">Belum ada data ruangan untuk lokasi ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- BAGIAN TANDA TANGAN (Opsional) --}}
        <div class="row mt-5">
            <div class="col-7"></div>
            <div class="col-5 text-center">
                <p class="mb-0">Tasikmalaya, {{ date('d F Y') }}</p>
                <p>Pengurus Barang,</p>
                <br><br><br>
                <p><strong>( ____________________ )</strong><br>NIP. .................................</p>
            </div>
        </div>
    </div>
</body>
</html>