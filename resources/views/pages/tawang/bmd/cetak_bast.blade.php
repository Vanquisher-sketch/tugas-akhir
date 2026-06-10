<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BAST & Pakta Integritas - {{ $bast_nomor }}</title>
    <style>
        /* 🌟 MENGUNCI UKURAN KERTAS F4 & ATUR MARGIN BERSIH */
        @page {
            size: 215mm 330mm; /* Ukuran mutlak Kertas F4 */
            margin: 20mm 20mm 20mm 20mm; /* Margin seimbang agar konten pas */
        }
        
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        /* 🌟 SAKTI: Memaksa isi halaman melar penuh sesuai lebar kertas F4 */
        .page-container {
            width: 100%;
            max-width: 175mm; /* Mengunci lebar konten utama */
            margin: 0 auto;
            page-break-after: always;
        }

        /* Khusus halaman terakhir tidak perlu memicu page break kosong di akhir */
        .page-container:last-child {
            page-break-after: avoid;
        }

        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: bold; }

        /* Layout KOP Surat Melar Penuh */
        .tabel-kop {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 5px;
        }
        .tabel-kop td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .kop-logo {
            width: 80px; /* Ukuran logo disesuaikan agar pas */
            height: auto;
            padding-bottom: 10px;
        }
        .kop-utama { font-size: 14pt; font-weight: bold; letter-spacing: 0.5px; }
        .kop-sub { font-size: 16pt; font-weight: bold; letter-spacing: 1px; line-height: 1.2; }
        .kop-info { font-size: 10pt; line-height: 1.3; }
        .garis-kop {
            border-bottom: 4px double #000;
            margin-bottom: 25px;
            margin-top: 5px;
            width: 100%;
        }

        /* Tabel Data Identitas Pihak */
        .tabel-pihak {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        .tabel-pihak td {
            padding: 4px 0;
            vertical-align: top;
        }

        /* Tabel Spesifikasi Objek BMD Melar Penuh */
        .tabel-bmd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .tabel-bmd th, .tabel-bmd td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 11pt;
        }
        .tabel-bmd th {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #f5f5f5;
        }
        
        .tabel-ttd {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .tabel-ttd td {
            vertical-align: top;
        }
    </style>
</head>
<body>

    @php 
        \Carbon\Carbon::setLocale('id'); 
    @endphp

    {{-- ======================================================== --}}
    {{-- 📑 HALAMAN 1: PAKTA INTEGRITAS                            --}}
    {{-- ======================================================== --}}
    <div class="page-container">
        <table class="tabel-kop">
            <tr>
                <td style="width: 15%; text-align: left;">
                    <img src="{{ public_path('img/tsk.png') }}" class="kop-logo" alt="Logo Pemkot">
                </td>
                <td style="width: 85%; text-align: center; padding-right: 10%;">
                    <span class="kop-utama">PEMERINTAH KOTA TASIKMALAYA</span><br>
                    <span class="kop-sub">KECAMATAN {{ strtoupper($lokasi) }}</span><br>
                    <span class="kop-info">Jalan Siliwangi Nomor 72, Kota Tasikmalaya, Jawa Barat 46115, Telp. (0265) 331932</span><br>
                    <span class="kop-info">Laman http://tawangkec.tasikmalayakota.go.id/</span>
                </td>
            </tr>
        </table>
        <div class="garis-kop"></div>

        <div class="text-center" style="margin-bottom: 25px;">
            <span style="font-size: 13pt; font-weight: bold; letter-spacing: 0.5px;">PAKTA INTEGRITAS BARANG MILIK DAERAH</span>
        </div>

        <div class="text-justify" style="margin-bottom: 15px;">
            Saya yang bertanda tangan di bawah ini :
        </div>

        <table class="tabel-pihak" style="margin-left: 20px; margin-bottom: 20px;">
            <tr>
                <td style="width: 22%;">Nama</td>
                <td style="width: 3%;">:</td>
                <td style="font-weight: bold;">{{ $pegawai->nama ?? 'Drs. BOEDI SANTOSA' }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $pemakai_identitas ?? '19750205 199311 1 002' }}</td>
            </tr>
            <tr>
                <td>Pangkat/Gol.</td>
                <td>:</td>
                <td>{{ $pegawai->pangkat_gol ?? 'Pembina Tk.I / IV.b' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $pegawai->jabatan ?? 'Camat Tawang' }}</td>
            </tr>
        </table>

        <div class="text-justify" style="margin-bottom: 15px;">
            Dengan ini menyatakan sebagai berikut :
            <ol style="margin-top: 5px; padding-left: 20px; text-align: justify; line-height: 1.6;">
                <li>Bahwa setelah menjalankan tugas sebagai {{ $pegawai->jabatan ?? 'Camat Tawang' }}, saya akan menyerahkan Barang Milik Daerah yang digunakan dalam rangka membantu Tugas Jabatan sebagaimana Berita Acara Pemegang Barang Milik Daerah terlampir;</li>
                <li>Bahwa Pakta Integritas Penyerahan Barang Milik Daerah yang saya tanda tangani ini berlaku juga sebagai Surat Kuasa kepada Badan Pengelola Keuangan dan Aset Daerah Kota Tasikmalaya untuk menarik kembali secara langsung Barang Milik Daerah saat tidak menjabat;</li>
                <li>Bahwa apabila saya melanggar hal-hal yang saya nyatakan dalam Pakta Integritas ini, saya bersedia bertanggung jawab mutlak dan siap dikenakan sanksi sesuai dengan peraturan dan perundang-undangan yang berlaku.</li>
            </ol>
        </div>

        <div class="text-justify" style="margin-bottom: 40px;">
            Demikian Pakta Integritas dan Surat Kuasa ini saya buat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya.
        </div>

        <table class="tabel-ttd" style="margin-top: 40px;">
            <tr>
                <td style="width: 50%;"></td>
                <td class="text-center" style="width: 50%;">
                    <span>Tasikmalaya, {{ \Carbon\Carbon::parse($bast_tanggal)->translatedFormat('d F Y') }}</span><br>
                    <span>Yang Membuat Pernyataan dan Pemberi Kuasa</span>
                    <div style="height: 80px;"></div>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $pegawai->nama ?? 'Drs. BOEDI SANTOSA' }}</span><br>
                    <span>{{ $pegawai->pangkat_gol ?? 'Pembina Tk.I / IV.b' }}</span><br>
                    <span>NIP. {{ $pemakai_identitas ?? '19750205 199311 1 002' }}</span>
                </td>
            </tr>
        </table>
    </div>


    {{-- ======================================================== --}}
    {{-- 📑 HALAMAN 2: BERITA ACARA SERAH TERIMA (BAST)            --}}
    {{-- ======================================================== --}}
    <div class="page-container">
        <table class="tabel-kop">
            <tr>
                <td style="width: 15%; text-align: left;">
                    <img src="{{ public_path('img/tsk.png') }}" class="kop-logo" alt="Logo Pemkot">
                </td>
                <td style="width: 85%; text-align: center; padding-right: 10%;">
                    <span class="kop-utama">PEMERINTAH KOTA TASIKMALAYA</span><br>
                    <span class="kop-sub">KECAMATAN {{ strtoupper($lokasi) }}</span><br>
                    <span class="kop-info">Jalan Siliwangi Nomor 72, Kota Tasikmalaya, Jawa Barat 46115, Telp. (0265) 331932</span><br>
                    <span class="kop-info">Laman http://tawangkec.tasikmalayakota.go.id/</span>
                </td>
            </tr>
        </table>
        <div class="garis-kop"></div>

        <div class="text-center" style="margin-bottom: 20px; line-height: 1.3;">
            <span style="font-size: 12pt; font-weight: bold; letter-spacing: 0.5px;">BERITA ACARA SERAH TERIMA</span><br>
            <span>Nomor : {{ $bast_nomor }}</span>
        </div>

        <div class="text-justify" style="margin-bottom: 15px;">
            Pada hari ini, Tanggal <strong>{{ \Carbon\Carbon::parse($bast_tanggal)->translatedFormat('d F Y') }}</strong>, kami yang bertanda tangan di bawah ini:
        </div>

        {{-- PIHAK KESATU --}}
        <table class="tabel-pihak" style="margin-left: 20px;">
            <tr>
                <td style="width: 15%;">Nama</td>
                <td style="width: 3%;">:</td>
                <td style="font-weight: bold;">{{ $bendahara->nama ?? 'Drs. H. ASEP GOPARULLAH, M.Pd' }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $bendahara->nip ?? '19700215 198903 1 004' }}</td>
            </tr>
            <tr>
                <td>Pangkat/Gol</td>
                <td>:</td>
                <td>{{ $bendahara->pangkat_gol ?? 'Pembina Utama Madya /  IV.d' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $bendahara->jabatan ?? 'Sekretaris Daerah Kota Tasikmalaya' }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $bendahara->alamat ?? 'Jl. Letnan Harun No. 1 Kota Tasikmalaya' }}</td>
            </tr>
        </table>
        <div style="margin-bottom: 15px;" class="text-justify">
            Atas nama Pemerintah Kota Tasikmalaya dalam kedudukannya Selaku Pengelola Barang selanjutnya disebut sebagai <strong>PIHAK KESATU</strong>.
        </div>

        {{-- PIHAK KEDUA --}}
        <table class="tabel-pihak" style="margin-left: 20px;">
            <tr>
                <td style="width: 15%;">Nama</td>
                <td style="width: 3%;">:</td>
                <td style="font-weight: bold;">{{ $pegawai->nama ?? 'Drs. BOEDI SANTOSA' }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $pemakai_identitas ?? '19750205 199311 1 002' }}</td>
            </tr>
            <tr>
                <td>Pangkat/Gol</td>
                <td>:</td>
                <td>{{ $pegawai->pangkat_gol ?? 'Pembina Tk.I / IV.b' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $pegawai->jabatan ?? 'Camat Tawang' }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>Jl. Siliwangi No. 72 Kota Tasikmalaya</td>
            </tr>
        </table>
        <div style="margin-bottom: 15px;" class="text-justify">
            Atas nama Pemerintah Kota Tasikmalaya dalam kedudukannya Selaku Pemegang Barang Milik Daerah pada Kantor Kecamatan Tawang selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.
        </div>

        <div class="text-justify" style="margin-bottom: 10px;">
            <strong>PIHAK KESATU</strong> menyerahkan Barang Milik Daerah kepada <strong>PIHAK KEDUA</strong> dengan spesifikasi sebagai berikut:
        </div>

        <table class="tabel-bmd">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 32%;">Jenis Barang/ Nama Barang</th>
                    <th style="width: 23%;">Merk</th>
                    <th style="width: 15%;">No. Polisi</th>
                    <th style="width: 10%;">Tahun</th>
                    <th style="width: 15%;">Harga Perolehan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1.</td>
                    <td><strong>{{ $peralatan->nama_barang ?? 'Mobil' }}</strong></td>
                    <td>{{ $peralatan->merk_tipe ?? 'Honda Mobilio DD4 1.5EMTCKD' }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $peralatan->nomor_polisi ?? 'Z 1562 H' }}</td>
                    <td class="text-center">{{ $peralatan->tahun_perolehan ?? '2017' }}</td>
                    <td class="text-right">
                        @if(isset($peralatan->nilai_perolehan))
                            {{ number_format($peralatan->nilai_perolehan, 2, ',', '.') }}
                        @else
                            204.500.000,00
                        @endif
                    </td>
                </tr>
                <tr style="font-weight: bold; background-color: #fafafa;">
                    <td colspan="5" class="text-center">Jumlah</td>
                    <td class="text-right">
                        @if(isset($peralatan->nilai_perolehan))
                            {{ number_format($peralatan->nilai_perolehan, 2, ',', '.') }}
                        @else
                            204.500.000,00
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ======================================================== --}}
    {{-- 📑 HALAMAN 3: PENUTUP & TANDA TANGAN (POLOS)              --}}
    {{-- ======================================================== --}}
    <div class="page-container">
        <div style="height: 20px;"></div>

        <div class="text-justify" style="margin-bottom: 20px;">
            <strong>PIHAK KEDUA</strong> menerima Barang Milik Daerah tersebut dari <strong>PIHAK KESATU</strong> untuk dipergunakan sebagaimana mestinya dalam rangka menunjang kelancaran pelaksanaan tugas kedinasan.
        </div>
        
        <div class="text-justify" style="margin-bottom: 50px;">
            Demikian Berita Acara ini dibuat dalam rangkap 3 (tiga) masing-masing rangkap memiliki kekuatan hukum yang sama untuk dipergunakan sebagaimana mestinya.
        </div>

        <div class="text-right" style="margin-bottom: 20px; padding-right: 50px;">
            Dibuat di Tasikmalaya
        </div>
        
        <table class="tabel-ttd text-center">
            <tr>
                <td style="width: 50%;">
                    <span>PIHAK KEDUA,</span>
                    <div style="height: 100px;"></div>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $pegawai->nama ?? 'Drs. BOEDI SANTOSA' }}</span><br>
                    <span>NIP. {{ $pemakai_identitas ?? '19750205 199311 1 002' }}</span>
                </td>
                <td style="width: 50%;">
                    <span>PIHAK KESATU,</span>
                    <div style="height: 100px;"></div>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $bendahara->nama ?? 'Drs. H. ASEP GOPARULLAH, M.Pd' }}</span><br>
                    <span>NIP. {{ $bendahara->nip ?? '19700215 198903 1 004' }}</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- TRIGGER WINDOWS PRINT DIRECT --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>