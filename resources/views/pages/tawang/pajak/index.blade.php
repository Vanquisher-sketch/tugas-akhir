@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Monitoring Pajak & Dokumen - {{ ucfirst($lokasi ?? 'Semua Lokasi') }}</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data BMD, Pajak & Kelengkapan Dokumen</h6>
            
            <div class="mt-2 mt-md-0">
                @if(isset($lokasi))
                <a href="{{ route('lokasi.pajak.print', $lokasi) }}" class="btn btn-danger btn-sm shadow-sm" target="_blank">
                    <i class="fas fa-print fa-sm text-white-50"></i> PDF
                </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0" style="font-size: 11px; color: #333; vertical-align: middle;">
                    <thead class="thead-light text-center">
                        <tr>
                            <th rowspan="2" class="align-middle font-weight-bold" style="width: 3%;">No</th>
                            <th rowspan="2" class="align-middle font-weight-bold" style="width: 15%;">Info Aset & Lokasi</th>
                            
                            <th colspan="2" class="align-middle font-weight-bold" style="background-color: #f8f9fa;">Data Pemakai</th>

                            <th colspan="4" class="align-middle font-weight-bold text-dark" style="background-color: #ffeeba;">Monitoring Pajak</th>
                            
                            <th colspan="3" class="align-middle font-weight-bold" style="background-color: #e3f2fd;">Dokumen BAST</th>
                            
                            <th colspan="3" class="align-middle font-weight-bold" style="background-color: #e8f5e9;">Dokumen Lain</th>
                            
                            <th rowspan="2" class="align-middle font-weight-bold">Aksi</th>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Detail Personal</th>
                            <th style="background-color: #f8f9fa;">Kontak</th>

                            <th style="background-color: #fff3cd;">Jatuh Tempo</th>
                            <th style="background-color: #fff3cd;">5 Tahunan (STNK)</th>
                            <th style="background-color: #fff3cd;">WA User</th>
                            <th style="background-color: #fff3cd;">WA Bendahara</th>

                            <th style="background-color: #eff8ff;">Nomor</th>
                            <th style="background-color: #eff8ff;">Tanggal</th>
                            <th style="background-color: #eff8ff;">File</th>

                            <th style="background-color: #f1f8e9;">Nama Dok</th>
                            <th style="background-color: #f1f8e9;">Nomor</th>
                            <th style="background-color: #f1f8e9;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pajaks as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration + ($pajaks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pajaks->firstItem() - 1 : 0) }}</td>
                            
                            <td class="align-middle">
                                <div class="font-weight-bold text-primary">{{ $item->peralatan->nama_barang ?? 'Nama Barang Tidak Ditemukan' }}</div>
                                <div class="small text-muted mb-2">
                                    {{ $item->peralatan->nomor_polisi ?? $item->peralatan->merk_tipe ?? '-' }} <br>
                                    Kode: {{ $item->peralatan->kode_barang ?? '-' }}
                                </div>
                                <hr class="my-1">
                                <div class="font-weight-bold">{{ $item->lokasi }}</div>
                                <div class="small text-muted font-italic">{{ $item->alamat_penggunaan }}</div>
                            </td>
                            
                            <td class="align-middle">
                                <b>{{ $item->pemakai_nama }}</b><br>
                                <span class="badge badge-secondary">{{ $item->pemakai_status }}</span><br>
                                <small>{{ $item->pemakai_jabatan ?? '-' }}</small>
                            </td>

                            <td class="align-middle text-center">
                                @if($item->nomor_pemakai)
                                    {{ $item->nomor_pemakai }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center align-middle" style="background-color: #fffdf0;">
                                @if($item->tanggal_pajak)
                                    @php
                                        $tgl = \Carbon\Carbon::parse($item->tanggal_pajak);
                                        $isLate = $tgl->isPast(); // Sudah lewat
                                        $isNear = $tgl->diffInDays(now()) < 30 && !$isLate; // Kurang dari 30 hari
                                    @endphp
                                    <span class="badge {{ $isLate ? 'badge-danger' : ($isNear ? 'badge-warning text-dark' : 'badge-success') }} shadow-sm p-1">
                                        {{ $tgl->format('d/m/Y') }}
                                    </span>
                                    @if($isLate)
                                        <div style="font-size: 9px;" class="text-danger font-weight-bold mt-1">TERLAMBAT</div>
                                    @elseif($isNear)
                                        <div style="font-size: 9px;" class="text-warning font-weight-bold mt-1">SEGERA</div>
                                    @endif
                                @else - @endif
                            </td>

                            <td class="text-center align-middle" style="background-color: #fff8d6;">
                                @if($item->tanggal_stnk)
                                    @php
                                        $tglS = \Carbon\Carbon::parse($item->tanggal_stnk);
                                        $isLateS = $tglS->isPast();
                                        $isNearS = $tglS->diffInDays(now()) < 60 && !$isLateS;
                                    @endphp
                                    <span class="badge {{ $isLateS ? 'badge-danger' : ($isNearS ? 'badge-warning text-dark' : 'badge-info') }} shadow-sm p-1">
                                        {{ $tglS->format('d/m/Y') }}
                                    </span>
                                @else - @endif
                            </td>

                            <td class="text-center align-middle" style="background-color: #fffdf0;">
                                @if($item->nomor_pemakai)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->nomor_pemakai)) }}?text=Halo%20{{ urlencode($item->pemakai_nama) }},%20mengingatkan%20pajak%20kendaraan%20{{ urlencode($item->peralatan->nomor_polisi ?? '') }}%20jatuh%20tempo%20pada%20{{ $item->tanggal_pajak }}" target="_blank" class="btn btn-success btn-circle btn-sm" title="Hubungi Pemakai">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="background-color: #fffdf0;">
                                @if($item->nomor_bendahara)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->nomor_bendahara)) }}" target="_blank" class="btn btn-info btn-circle btn-sm" title="Hubungi Bendahara">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                            </td>

                            <td class="align-middle text-break" style="max-width: 100px;">{{ $item->bast_nomor ?? '-' }}</td>
                            <td class="text-center align-middle">
                                {{ $item->bast_tanggal ? \Carbon\Carbon::parse($item->bast_tanggal)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center align-middle">
                                @if($item->bast_file)
                                    <a href="{{ asset('storage/' . $item->bast_file) }}" target="_blank" class="text-primary font-weight-bold">
                                        <i class="fas fa-file-pdf fa-lg"></i>
                                    </a>
                                @else <span class="text-muted">-</span> @endif
                            </td>

                            <td class="align-middle text-break" style="max-width: 100px;">{{ $item->dokumen_lain_nama ?? '-' }}</td>
                            <td class="align-middle text-break" style="max-width: 100px;">{{ $item->dokumen_lain_nomor ?? '-' }}</td>
                            <td class="text-center align-middle">
                                {{ $item->dokumen_lain_tanggal ? \Carbon\Carbon::parse($item->dokumen_lain_tanggal)->format('d/m/Y') : '-' }}
                            </td>

                            <td class="text-center align-middle">
                                <a href="{{ route('bmds.edit', $item->id) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center py-5 text-gray-500">
                                <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i><br>
                                Data belum tersedia.
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