@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Monitoring Pajak & Dokumen - {{ ucfirst($lokasi) }}</h1>
    </div>

    <!-- Card Container -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data Aset, Pajak & Kelengkapan Dokumen</h6>
            
            <!-- Tombol Aksi -->
            <div class="mt-2 mt-md-0">
                <!-- Tombol Print -->
                <a href="{{ route('lokasi.pajak.print', $lokasi) }}" class="btn btn-danger btn-sm shadow-sm" target="_blank">
                    <i class="fas fa-print fa-sm text-white-50"></i> PDF
                </a>

                <!-- Tombol Blast WA -->
                <form action="{{ route('lokasi.pajak.kirim_reminder', $lokasi) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Kirim notifikasi WA ke semua aset yang jatuh tempo?');">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm shadow-sm text-dark">
                        <i class="fab fa-whatsapp fa-sm"></i> Kirim Reminder
                    </button>
                </form>

                <!-- Tombol Excel -->
                <a href="{{ route('lokasi.export.excel', ['lokasi' => $lokasi, 'menu' => 'bmd']) }}" class="btn btn-success btn-sm shadow-sm ml-1" target="_blank">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Excel
                </a>
            </div>
        </div>

        <!-- Filter Search -->
        <div class="card-body border-bottom">
            <form action="" method="GET" class="form-inline float-right">
                <div class="input-group">
                    <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Cari aset / nopol..." value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" onclick="this.form.submit()">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0" style="font-size: 10px; color: #333;">
                    <thead class="thead-light text-center">
                        <tr>
                            <!-- BARIS 1: GROUP HEADER -->
                            <th rowspan="2" class="align-middle font-weight-bold">No</th>
                            <th rowspan="2" class="align-middle font-weight-bold">Info Aset</th>
                            <th rowspan="2" class="align-middle font-weight-bold">Lokasi</th>
                            
                            <!-- Group Pemakai -->
                            <th colspan="2" class="align-middle font-weight-bold">Pemakai</th>

                            <!-- Group Pajak (Kuning) -->
                            <th colspan="4" class="align-middle font-weight-bold text-dark" style="background-color: #ffeeba;">Monitoring Pajak</th>
                            
                            <!-- Group Dokumen BAST (Biru Muda) -->
                            <th colspan="3" class="align-middle font-weight-bold" style="background-color: #e3f2fd;">Dokumen BAST</th>
                            
                            <!-- Group Dokumen Lain (Hijau Muda) -->
                            <th colspan="3" class="align-middle font-weight-bold" style="background-color: #e8f5e9;">Dokumen Lain</th>
                            
                            <th rowspan="2" class="align-middle font-weight-bold">Ket</th>
                            <th rowspan="2" class="align-middle font-weight-bold">Aksi</th>
                        </tr>
                        <tr>
                            <!-- BARIS 2: SUB HEADER -->
                            
                            <!-- Sub Pemakai -->
                            <th>Nama</th>
                            <th>No. HP</th>

                            <!-- Sub Pajak -->
                            <th style="background-color: #fff3cd;">Tahunan</th>
                            <th style="background-color: #ffe08a;">5 Tahunan</th>
                            <th style="background-color: #fff3cd;">WA User</th>
                            <th style="background-color: #fff3cd;">WA Bendahara</th>

                            <!-- Sub BAST -->
                            <th style="background-color: #eff8ff;">Nomor</th>
                            <th style="background-color: #eff8ff;">Tanggal</th>
                            <th style="background-color: #eff8ff;">File</th>

                            <!-- Sub Dokumen Lain -->
                            <th style="background-color: #f1f8e9;">Nama Dok</th>
                            <th style="background-color: #f1f8e9;">Nomor</th>
                            <th style="background-color: #f1f8e9;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pajaks as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + $pajaks->firstItem() - 1 }}</td>
                            
                            <!-- Info Aset (Digabung biar hemat tempat) -->
                            <td>
                                <b>{{ $item->peralatan->nama_barang ?? '-' }}</b><br>
                                <span class="text-muted">{{ $item->peralatan->nomor_polisi ?? $item->peralatan->merk_tipe ?? '-' }}</span><br>
                                <small>{{ $item->peralatan->kode_barang ?? '-' }}</small>
                            </td>
                            
                            <td>{{ $item->alamat_penggunaan }}</td>
                            
                            <!-- Data Pemakai -->
                            <td>{{ $item->pemakai_nama }}</td>
                            <td>{{ $item->nomor_pemakai ?? '-' }}</td>

                            <!-- 1. PAJAK TAHUNAN -->
                            <td class="text-center" style="background-color: #fffdf0;">
                                @if($item->tanggal_pajak)
                                    @php
                                        $tgl = \Carbon\Carbon::parse($item->tanggal_pajak);
                                        $isLate = $tgl->isPast();
                                        $isNear = $tgl->diffInDays(now()) < 30 && !$isLate;
                                    @endphp
                                    <span class="badge {{ $isLate ? 'badge-danger' : ($isNear ? 'badge-warning text-dark' : 'badge-success') }} shadow-sm">
                                        {{ $tgl->format('d/m/Y') }}
                                    </span>
                                @else - @endif
                            </td>

                            <!-- 2. PAJAK 5 TAHUNAN (STNK) -->
                            <td class="text-center" style="background-color: #fff8d6;">
                                @if($item->tanggal_stnk)
                                    @php
                                        $tglS = \Carbon\Carbon::parse($item->tanggal_stnk);
                                        $isLateS = $tglS->isPast();
                                        $isNearS = $tglS->diffInDays(now()) < 60 && !$isLateS;
                                    @endphp
                                    <span class="badge {{ $isLateS ? 'badge-danger' : ($isNearS ? 'badge-warning text-dark' : 'badge-info') }} shadow-sm">
                                        {{ $tglS->format('d/m/Y') }}
                                    </span>
                                @else - @endif
                            </td>

                            <!-- Tombol WA -->
                            <td class="text-center" style="background-color: #fffdf0;">
                                @if($item->nomor_pemakai)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $item->nomor_pemakai) }}" target="_blank" class="text-success"><i class="fab fa-whatsapp"></i></a>
                                @endif
                            </td>
                            <td class="text-center" style="background-color: #fffdf0;">
                                @if($item->nomor_bendahara)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $item->nomor_bendahara) }}" target="_blank" class="text-info"><i class="fab fa-whatsapp"></i></a>
                                @endif
                            </td>

                            <!-- DATA BAST (Sesuai Model) -->
                            <td>{{ $item->bast_nomor ?? '-' }}</td>
                            <td class="text-center">
                                {{ $item->bast_tanggal ? \Carbon\Carbon::parse($item->bast_tanggal)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @if($item->bast_file)
                                    <a href="{{ asset('storage/' . $item->bast_file) }}" target="_blank" class="text-primary"><i class="fas fa-file-alt"></i></a>
                                @else - @endif
                            </td>

                            <!-- DATA DOKUMEN LAIN (Sesuai Model) -->
                            <td>{{ $item->dokumen_lain_nama ?? '-' }}</td>
                            <td>{{ $item->dokumen_lain_nomor ?? '-' }}</td>
                            <td class="text-center">
                                {{ $item->dokumen_lain_tanggal ? \Carbon\Carbon::parse($item->dokumen_lain_tanggal)->format('d/m/Y') : '-' }}
                            </td>

                            <!-- Keterangan -->
                            <td>{{ $item->keterangan ?? '-' }}</td>

                            <!-- Aksi -->
                            <td class="text-center">
                                <a href="{{ route('lokasi.pajak.edit', [$lokasi, $item->id]) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="16" class="text-center py-5 text-gray-500">
                                <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i><br>
                                Data belum tersedia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                {{ $pajaks->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection