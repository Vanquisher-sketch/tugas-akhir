<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penggunaan BMD</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 4px; vertical-align: top; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="text-center header">
        <h2 style="margin:0;">DAFTAR PENGGUNAAN BARANG MILIK DAERAH</h2>
        <h3 style="margin:5px 0;">PERALATAN DAN MESIN</h3>
        <p>KECAMATAN {{ strtoupper($lokasi) }} - KOTA TASIKMALAYA</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kode Barang</th>
                <th rowspan="2">Nama Barang / Jenis</th>
                <th rowspan="2">Lokasi/Alamat</th>
                <th colspan="4">Data Pemakai</th>
                <th colspan="2">Dokumen BAST</th>
                <th rowspan="2">Ket.</th>
            </tr>
            <tr>
                <th>Nama</th>
                <th>Status</th>
                <th>Jabatan</th>
                <th>Identitas</th>
                <th>Nomor</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bmds as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->peralatan->kode_barang ?? '-' }}</td>
                <td>
                    {{ $item->peralatan->nama_barang ?? '-' }}<br>
                    <small>Reg: {{ $item->peralatan->nibr }}</small>
                </td>
                <td>{{ $item->alamat_penggunaan }}</td>
                
                <td>{{ $item->pemakai_nama }}</td>
                <td>{{ $item->pemakai_status }}</td>
                <td>{{ $item->pemakai_jabatan }}</td>
                <td>{{ $item->pemakai_identitas }}</td>
                
                <td>{{ $item->bast_nomor }}</td>
                <td>{{ $item->bast_tanggal ? $item->bast_tanggal->format('d/m/Y') : '-' }}</td>
                
                <td>{{ $item->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    
    <div style="width: 100%; display: table;">
        <div style="display: table-row;">
            <div style="display: table-cell; width: 60%;"></div> <div style="display: table-cell; text-align: center;">
                Tasikmalaya, {{ date('d F Y') }} <br>
                Kepala Badan Pengelola Keuangan dan Aset Daerah<br>
                <br><br><br><br>
                <strong>(Nama Pejabat Disini)</strong><br>
                NIP. ...........................
            </div>
        </div>
    </div>

</body>
</html>