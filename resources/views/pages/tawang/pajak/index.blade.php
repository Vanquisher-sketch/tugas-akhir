@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Monitoring Pajak & Dokumen - {{ ucfirst($lokasi ?? 'Semua Lokasi') }}</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-center justify-content-between border-bottom-primary">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice-dollar mr-2"></i>Data BMD, Pajak & Kelengkapan Dokumen</h6>
            
            <div class="mt-2 mt-md-0">
                @if(isset($lokasi))
                <a href="{{ route('lokasi.pajak.print', $lokasi) }}" class="btn btn-danger btn-sm shadow-sm" target="_blank">
                    <i class="fas fa-file-pdf fa-sm text-white-50 mr-1"></i> Cetak PDF
                </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            {{-- Notifikasi otomatis akan muncul via SweetAlert2 dari app.blade.php --}}

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-dark" id="dataTable" width="100%" cellspacing="0" style="font-size: 12px; vertical-align: middle;">
                    <thead class="thead-light text-center">
                        <tr class="font-weight-bold">
                            <th rowspan="2" class="align-middle" style="width: 3%;">No</th>
                            <th rowspan="2" class="align-middle" style="width: 15%;">Info Aset & Lokasi</th>
                            <th colspan="2" class="align-middle bg-light">Data Pemakai</th>
                            <th colspan="4" class="align-middle text-dark" style="background-color: #ffeeba;">Monitoring Pajak</th>
                            <th colspan="3" class="align-middle" style="background-color: #e3f2fd;">Dokumen BAST</th>
                            <th rowspan="2" class="align-middle">Aksi</th>
                        </tr>
                        <tr class="font-weight-bold">
                            <th class="bg-light">Detail Personal</th>
                            <th class="bg-light text-center">Kontak</th>
                            <th style="background-color: #fff3cd;">Jatuh Tempo</th>
                            <th style="background-color: #fff3cd;">STNK (5 Thn)</th>
                            <th style="background-color: #fff3cd;" title="Kirim Pesan ke User">WA User</th>
                            <th style="background-color: #fff3cd;" title="Kirim Pesan ke Bendahara">WA Bend</th>
                            <th style="background-color: #eff8ff;">Nomor</th>
                            <th style="background-color: #eff8ff;">Tanggal</th>
                            <th style="background-color: #eff8ff;">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pajaks as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration + ($pajaks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pajaks->firstItem() - 1 : 0) }}</td>
                            
                            <td class="align-middle">
                                <div class="font-weight-bold text-primary">{{ $item->peralatan->nama_barang ?? 'N/A' }}</div>
                                <div class="small text-muted">
                                    {{ $item->peralatan->nomor_polisi ?? '-' }} <br>
                                    <span class="font-italic text-info">{{ $item->lokasi }}</span>
                                </div>
                            </td>
                            
                            <td class="align-middle">
                                <b>{{ $item->pemakai_nama }}</b><br>
                                <span class="badge badge-secondary small">{{ $item->pemakai_status }}</span>
                            </td>

                            <td class="align-middle text-center font-weight-bold">
                                {{ $item->nomor_pemakai ?? '-' }}
                            </td>

                            {{-- KOLOM JATUH TEMPO --}}
                            <td class="text-center align-middle" style="background-color: #fffdf0;">
                                @if($item->tanggal_pajak)
                                    @php
                                        $tgl = \Carbon\Carbon::parse($item->tanggal_pajak);
                                        $diff = now()->diffInDays($tgl, false);
                                    @endphp
                                    
                                    @if($diff < 0)
                                        <span class="badge badge-danger p-1 shadow-sm animate__animated animate__pulse animate__infinite">
                                            {{ $tgl->format('d/m/Y') }}
                                        </span>
                                        <div style="font-size: 9px;" class="text-danger font-weight-bold mt-1">LEWAT {{ abs($diff) }} HARI</div>
                                    @elseif($diff <= 30)
                                        <span class="badge badge-warning text-dark p-1 shadow-sm">
                                            {{ $tgl->format('d/m/Y') }}
                                        </span>
                                        <div style="font-size: 9px;" class="text-dark font-weight-bold mt-1">{{ $diff }} HARI LAGI</div>
                                    @else
                                        <span class="badge badge-success p-1 shadow-sm">
                                            {{ $tgl->format('d/m/Y') }}
                                        </span>
                                    @endif
                                @else - @endif
                            </td>

                            {{-- STNK 5 TAHUNAN --}}
                            <td class="text-center align-middle" style="background-color: #fff8d6;">
                                {{ $item->tanggal_stnk ? \Carbon\Carbon::parse($item->tanggal_stnk)->format('d/m/Y') : '-' }}
                            </td>

                            {{-- LINK WHATSAPP USER --}}
                            <td class="text-center align-middle" style="background-color: #fffdf0;">
                                @if($item->nomor_pemakai)
                                    @php
                                        $waUser = preg_replace('/[^0-9]/', '', $item->nomor_pemakai);
                                        $waUser = preg_replace('/^0/', '62', $waUser);
                                        $msgUser = "Halo Pak/Bu {$item->pemakai_nama}, mengingatkan pajak kendaraan {$item->peralatan->nomor_polisi} akan jatuh tempo pada " . ($item->tanggal_pajak ? \Carbon\Carbon::parse($item->tanggal_pajak)->format('d/m/Y') : '');
                                    @endphp
                                    <a href="https://wa.me/{{ $waUser }}?text={{ urlencode($msgUser) }}" target="_blank" class="btn btn-success btn-sm shadow-sm" title="Kirim WA">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                            </td>

                            {{-- LINK WHATSAPP BENDAHARA --}}
                            <td class="text-center align-middle" style="background-color: #fffdf0;">
                                @if($item->nomor_bendahara)
                                    @php
                                        $waBend = preg_replace('/[^0-9]/', '', $item->nomor_bendahara);
                                        $waBend = preg_replace('/^0/', '62', $waBend);
                                    @endphp
                                    <a href="https://wa.me/{{ $waBend }}" target="_blank" class="btn btn-info btn-sm shadow-sm" title="Hubungi Bendahara">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                            </td>

                            <td class="align-middle">{{ $item->bast_nomor ?? '-' }}</td>
                            <td class="text-center align-middle small">{{ $item->bast_tanggal ? \Carbon\Carbon::parse($item->bast_tanggal)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center align-middle">
                                @if($item->bast_file)
                                    <a href="{{ asset('storage/' . $item->bast_file) }}" target="_blank" class="text-danger">
                                        <i class="fas fa-file-pdf fa-lg"></i>
                                    </a>
                                @else - @endif
                            </td>

                            <td class="text-center align-middle">
                                <a href="{{ route('bmds.edit', $item->id) }}" class="btn btn-warning btn-sm shadow-sm mr-1">
                                    <i class="fas fa-edit fa-sm"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-gray-500">
                                <i class="fas fa-search fa-3x mb-3 text-gray-300"></i><br>
                                Data monitoring pajak belum tersedia.
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