@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Monitoring Pajak & Dokumen - Kelurahan {{ ucfirst($lokasi ?? 'Semua Lokasi') }}</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-center justify-content-between border-bottom-primary">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice-dollar mr-2"></i>Sistem Monitoring Pajak Otomatis</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-dark" id="dataTable" width="100%" cellspacing="0" style="font-size: 12px; vertical-align: middle;">
                    <thead class="thead-light text-center">
                        <tr class="font-weight-bold">
                            <th rowspan="2" class="align-middle" style="width: 3%;">No</th>
                            <th rowspan="2" class="align-middle" style="width: 22%;">Identitas Kendaraan Dinas</th>
                            <th colspan="2" class="align-middle bg-light">Data Pemegang / Pemakai</th>
                            <th colspan="3" class="align-middle text-dark" style="background-color: #ffeeba;">Status Pajak & Legalitas</th>
                            <th colspan="3" class="align-middle" style="background-color: #e3f2fd;">Dokumen Kelengkapan BAST</th>
                        </tr>
                        <tr class="font-weight-bold">
                            <th class="bg-light">Nama Pegawai</th>
                            <th class="bg-light text-center">Email Pegawai</th>
                            <th style="background-color: #fff3cd;">Jatuh Tempo Pajak</th>
                            <th style="background-color: #fff3cd;">Masa Berlaku STNK</th>
                            <th style="background-color: #fff3cd;" title="Kirim Pesan Pengingat Pajak">Kirim Warning</th>
                            <th style="background-color: #eff8ff;">Nomor BAST</th>
                            <th style="background-color: #eff8ff;">Tanggal</th>
                            <th style="background-color: #eff8ff;">Berkas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pajaks as $item)
                        @php
                            $tglPajak = $item->alat_tanggal_pajak ? \Carbon\Carbon::parse($item->alat_tanggal_pajak) : null;
                            $diffDays = $tglPajak ? \Carbon\Carbon::today()->diffInDays($tglPajak, false) : 0;
                            
                            $rowStyle = '';
                            if ($tglPajak && $diffDays < 0) {
                                $rowStyle = 'background-color: #f8d7da;'; 
                            } elseif ($tglPajak && $diffDays <= 30) {
                                $rowStyle = 'background-color: #fff3cd;'; 
                            }

                            $bmdAktif = $item->bmd ?? \App\Models\Bmd::where('bmd_alat_kode', $item->alat_kode_barang)->first();
                            
                            $emailPemegang = '-';
                            // ✅ REVISI: Ubah pemanggilan email menjadi pegawai_email sesuai dengan database
                            if ($bmdAktif && $bmdAktif->pegawai && $bmdAktif->pegawai->pegawai_email) {
                                $emailPemegang = $bmdAktif->pegawai->pegawai_email;
                            }
                        @endphp
                        <tr style="{{ $rowStyle }}">
                            <td class="text-center align-middle font-weight-bold">{{ $loop->iteration + ($pajaks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pajaks->firstItem() - 1 : 0) }}</td>
                            
                            {{-- Identitas Kendaraan --}}
                            <td class="align-middle">
                                <div class="font-weight-bold text-primary">{{ $item->alat_nama_barang }}</div>
                                <div class="small font-weight-bold mt-1">
                                    <span class="badge badge-dark px-2 py-1"><i class="fas fa-car-side mr-1"></i> {{ $item->alat_nomor_polisi ?? '-' }}</span>
                                </div>
                                <div class="small text-muted mt-1">Merk/Type: {{ $item->alat_merk_tipe ?? '-' }}</div>
                            </td>
                            
                            {{-- Data Pemakai --}}
                            <td class="align-middle">
                                @if($bmdAktif && $bmdAktif->pegawai)
                                    {{-- ✅ REVISI: Ubah pemanggilan nama menjadi pegawai_nama sesuai dengan database --}}
                                    <b class="text-gray-900">{{ $bmdAktif->pegawai->pegawai_nama }}</b><br>
                                    <span class="badge badge-primary small mt-1">{{ $bmdAktif->pemakai_status ?? 'ASN' }}</span>
                                @else
                                    <span class="badge badge-secondary p-1 font-weight-bold"><i class="fas fa-warehouse mr-1"></i> Stand-by di Kantor</span>
                                @endif
                            </td>

                            {{-- Tampilkan Teks Email Pemakai --}}
                            <td class="align-middle text-center font-weight-bold text-info">
                                {{ $emailPemegang }}
                            </td>

                            {{-- Jatuh Tempo Pajak --}}
                            <td class="text-center align-middle font-weight-bold">
                                @if($item->alat_tanggal_pajak)
                                    @if($diffDays < 0)
                                        <span class="badge badge-danger p-1 text-uppercase shadow-sm">
                                            <i class="fas fa-times-circle"></i> {{ $tglPajak->format('d/m/Y') }}
                                        </span>
                                        <div style="font-size: 10px;" class="text-danger font-weight-bold mt-1 text-uppercase">TERLEWAT {{ abs($diffDays) }} HARI</div>
                                    @elseif($diffDays <= 30)
                                        <span class="badge badge-warning text-dark p-1 shadow-sm">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $tglPajak->format('d/m/Y') }}
                                        </span>
                                        <div style="font-size: 10px;" class="text-dark font-weight-bold mt-1 text-uppercase">{{ $diffDays }} HARI LAGI</div>
                                    @else
                                        <span class="badge badge-success p-1 shadow-sm">
                                            <i class="fas fa-check-circle"></i> {{ $tglPajak->format('d/m/Y') }}
                                        </span>
                                    @endif
                                @else - @endif
                            </td>

                            {{-- STNK 5 Tahunan --}}
                            <td class="text-center align-middle font-weight-bold">
                                {{ $item->alat_tanggal_stnk ? \Carbon\Carbon::parse($item->alat_tanggal_stnk)->format('d/m/Y') : '-' }}
                            </td>

                            {{-- Email Warning Trigger (Tombol Mailto diubah ke Form Auto-Send) --}}
                            <td class="text-center align-middle">
                                @if($emailPemegang !== '-')
                                    @php
                                        // ✅ REVISI: Ubah pemanggilan nama menjadi pegawai_nama untuk subjek/body email
                                        $namaPemakai = ($bmdAktif && $bmdAktif->pegawai) ? $bmdAktif->pegawai->pegawai_nama : 'Bapak/Ibu';
                                        $txtStatus = $diffDays < 0 ? "TELAH LEWAT JATUH TEMPO" : "AKAN SEGERA JATUH TEMPO";
                                        
                                        $subjectEmail = "Pemberitahuan Pajak Kendaraan Dinas - " . $item->alat_nomor_polisi;
                                        $msgUser = "Halo Pak/Bu {$namaPemakai},\r\n\r\nMenginfokan bahwa pajak kendaraan dinas {$item->alat_nama_barang} ({$item->alat_nomor_polisi}) yang Anda pegang {$txtStatus} pada tanggal " . ($tglPajak ? $tglPajak->format('d/m/Y') : '') . ".\r\n\r\nMohon berkas pendukung segera disiapkan untuk proses perpanjangan pajak.\r\n\r\nTerima kasih.";
                                    @endphp
                                    
                                    {{-- 🌟 INI BAGIAN YANG BERUBAH: Menggunakan Form untuk Auto-Send SMTP --}}
                                    <form action="{{ route('lokasi.pajak.kirim_email', $lokasi) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ $emailPemegang }}">
                                        <input type="hidden" name="subject" value="{{ $subjectEmail }}">
                                        <input type="hidden" name="pesan" value="{{ $msgUser }}">
                                        <button type="submit" class="btn btn-info btn-sm shadow-sm font-weight-bold" title="Kirim Email Otomatis" onclick="return confirm('Kirim email peringatan otomatis ke {{ $namaPemakai }} ({{ $emailPemegang }})?')">
                                            <i class="fas fa-paper-plane mr-1"></i> Auto-Send
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-light border text-muted" style="font-size: 11px;" disabled title="Data email tidak ditemukan di database">
                                        <i class="fas fa-envelope-open mr-1"></i> No Email
                                    </button>
                                @endif
                            </td>

                            {{-- Dokumen BAST Relasional --}}
                            <td class="align-middle font-weight-bold text-center text-primary">
                                {{ $bmdAktif->bast_nomor ?? '-' }}
                            </td>
                            <td class="text-center align-middle small">
                                {{ ($bmdAktif && $bmdAktif->bast_tanggal) ? \Carbon\Carbon::parse($bmdAktif->bast_tanggal)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center align-middle">
                                @if($bmdAktif && $bmdAktif->bast_file)
                                    <a href="{{ route('lokasi.bmd.buka_pdf', ['lokasi' => $lokasi, 'id' => $bmdAktif->id]) }}" target="_blank" class="text-danger font-weight-bold">
                                        <i class="fas fa-file-pdf fa-lg"></i>
                                    </a>
                                @else - @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-gray-500">
                                <i class="fas fa-check-double fa-3x mb-3 text-success"></i><br>
                                <h6 class="font-weight-bold text-success">Luar Biasa, Aman Semua!</h6>
                                Tidak mendeteksi adanya kendaraan dinas yang pajaknya mati atau mendekati kritis (< 30 hari) di wilayah operasional ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                @if($pajaks instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $pajaks->withQueryString()->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection