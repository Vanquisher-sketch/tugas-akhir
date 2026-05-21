<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BAST - {{ $bast_nomor }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            padding: 10px;
        }
        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .font-weight-bold { font-weight: bold; }
        
        /* Tabel Rincian Aset KIB B */
        .tabel-bmd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .tabel-bmd th, .tabel-bmd td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 10.5pt;
        }
        .tabel-bmd th {
            background-color: #f2f2f2;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- 1. KOP SURAT DINAS --}}
    <div class="text-center" style="border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 20px;">
        <span style="font-size: 13pt; font-weight: bold;">PEMERINTAH KOTA TASIKMALAYA</span><br>
        <span style="font-size: 15pt; font-weight: bold;">KECAMATAN {{ strtoupper($lokasi) }}</span><br>
        <span style="font-size: 9pt; font-style: italic;">Jl. Alun-Alun No. 1, Kota Tasikmalaya, Jawa Barat, Kode Pos 46111</span>
    </div>

    {{-- 2. JUDUL BERITA ACARA --}}
    <div class="text-center" style="margin-bottom: 25px;">
        <span style="font-size: 12pt; font-weight: bold; text-decoration: underline;">BERITA ACARA SERAH TERIMA (BAST)</span><br>
        <span>Nomor: {{ $bast_nomor }}</span>
    </div>

    {{-- 3. PARAGRAF PEMBUKA --}}
    <div class="text-justify" style="margin-bottom: 15px; text-indent: 0.4in;">
        Pada hari ini, Tanggal <strong>{{ date('d-m-Y', strtotime($bast_tanggal)) }}</strong>, bertempat di Kantor Kecamatan {{ ucfirst($lokasi) }}, kami yang bertandatangan di bawah ini menyatakan telah melakukan serah terima Barang Milik Daerah (BMD) Peralatan dan Mesin dengan rincian pihak-pihak sebagai berikut:
    </div>

    {{-- 4. PIHAK PERTAMA (PENYERAH) --}}
    <div style="margin-bottom: 8px; font-weight: bold;"><i class="fas fa-user"></i> PIHAK PERTAMA (Yang Menyerahkan):</div>
    <table style="width: 100%; margin-left: 20px; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 25%; padding: 2px 0;">Nama Lengkap</td>
            <td style="width: 3%; padding: 2px 0;">:</td>
            <td style="font-weight: bold; padding: 2px 0;">{{ $bendahara->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">NIP</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;">{{ $bendahara->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Jabatan</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;">{{ $bendahara->jabatan ?? 'Bendahara Barang / Pengurus Barang Pengguna' }}</td>
        </tr>
    </table>

    {{-- 5. PIHAK KEDUA (PENERIMA) --}}
    <div style="margin-bottom: 8px; font-weight: bold;"><i class="fas fa-user"></i> PIHAK KEDUA (Yang Menerima / Pemakai):</div>
    <table style="width: 100%; margin-left: 20px; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 25%; padding: 2px 0;">Nama Lengkap</td>
            <td style="width: 3%; padding: 2px 0;">:</td>
            <td style="font-weight: bold; padding: 2px 0;">{{ $pegawai->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">NIP / NIK</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;">{{ $pemakai_identitas }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Status / Jabatan</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;">{{ $pegawai->jabatan ?? '-' }} ({{ $pemakai_status }})</td>
        </tr>
    </table>

    {{-- 6. PERNYATAAN HUBUNG --}}
    <div class="text-justify" style="margin-top: 15px; margin-bottom: 10px;">
        PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima dari PIHAK PERTAMA Barang Milik Daerah (BMD) dalam kondisi operasional yang baik, dengan rincian data sebagai berikut:
    </div>

    {{-- 7. TABEL DETAIL BARANG --}}
    <table class="tabel-bmd">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Kode & Register Aset</th>
                <th style="width: 45%;">Nama & Spesifikasi Barang</th>
                <th style="width: 20%;">Nomor Polisi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                <td style="vertical-align: top;">
                    <strong>{{ $peralatan->kode_barang ?? '-' }}</strong><br>
                    <small>Reg: {{ $peralatan->nomor_register ?? '-' }}</small>
                </td>
                <td style="vertical-align: top;">
                    <strong>{{ $peralatan->nama_barang ?? '-' }}</strong><br>
                    <small>Merk/Tipe: {{ $peralatan->merk_tipe ?? '-' }}</small><br>
                    <small style="color: #444;">Alamat Posisi: {{ $alamat_penggunaan }}</small>
                </td>
                <td class="text-center" style="vertical-align: top; font-weight: bold;">
                    {{ $peralatan->nomor_polisi ?? '-' }}
                </td>
            </tr>
            @if($keterangan)
            <tr>
                <td colspan="4" style="font-style: italic; font-size: 10pt; background-color: #fafafa; padding: 6px;">
                    <strong>Catatan Kondisi Khusus:</strong> {{ $keterangan }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- 8. PARAGRAF PENUTUP --}}
    <div class="text-justify" style="margin-bottom: 35px; text-indent: 0.4in;">
        Demikian Berita Acara Serah Terima ini dibuat secara sadar untuk dapat dipergunakan sebagaimana mestinya. Sejak penandatanganan surat ini dibuat, maka tanggung jawab operasional, pemeliharaan, serta keamanan fisik objek aset sepenuhnya beralih kepada PIHAK KEDUA.
    </div>

    {{-- 9. KOLOM TANDA TANGAN --}}
    <table style="width: 100%; margin-top: 25px; page-break-inside: avoid;">
        <tr>
            <td class="text-center" style="width: 50%; vertical-align: top;">
                Yang Menerima / Memakai<br>
                <strong>PIHAK KEDUA</strong>
                <div style="height: 70px;"></div>
                <span style="text-decoration: underline; font-weight: bold;">{{ $pegawai->nama ?? '-' }}</span><br>
                <span>NIP/NIK. {{ $pemakai_identitas }}</span>
            </td>
            <td class="text-center" style="width: 50%; vertical-align: top;">
                Yang Menyerahkan,<br>
                <strong>PIHAK PERTAMA</strong>
                <div style="height: 70px;"></div>
                <span style="text-decoration: underline; font-weight: bold;">{{ $bendahara->nama ?? '-' }}</span><br>
                <span>NIP. {{ $bendahara->nip ?? '-' }}</span>
            </td>
        </tr>
    </table>

</body>
</html>