<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penggunaan BMD - {{ strtoupper($lokasi) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @page { size: landscape; margin: 1cm; }
        body { font-family: 'Times New Roman', serif; font-size: 9pt; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; vertical-align: middle; }
        .text-center { text-align: center; }
        .header-title { font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <div class="text-center mb-4">
            <h5 class="mb-0 font-weight-bold">DAFTAR PENGGUNAAN BARANG MILIK DAERAH</h5>
            <h6 class="font-weight-bold">KECAMATAN {{ strtoupper($lokasi) }} - KOTA TASIKMALAYA</h6>
        </div>

        <table class="table table-bordered">
            <thead class="text-center bg-light">
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Kode Barang</th>
                    <th rowspan="2">Nama Barang / Jenis</th>
                    <th rowspan="2">Lokasi/Alamat</th>
                    <th colspan="3">Data Pemakai</th>
                    <th colspan="2">Dokumen BAST</th>
                    <th rowspan="2">Ket.</th>
                </tr>
                <tr>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Identitas</th>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bmds as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->peralatan_kode }}</td>
                    <td>{{ $item->peralatan->nama_barang ?? '-' }}</td>
                    <td>{{ $item->alamat_penggunaan }}</td>
                    <td>{{ $item->pemakai_nama }}</td>
                    <td>{{ $item->pemakai_jabatan }}</td>
                    <td>{{ $item->pemakai_identitas }}</td>
                    <td>{{ $item->bast_nomor }}</td>
                    <td class="text-center">{{ $item->bast_tanggal ? $item->bast_tanggal->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Tasikmalaya, {{ date('d F Y') }}</p>
                <p>Camat {{ ucfirst($lokasi) }},</p>
                <br><br><br>
                <p class="font-weight-bold"><u>(.......................................)</u></p>
                <p>NIP. ...........................</p>
            </div>
        </div>
    </div>
</body>
</html>