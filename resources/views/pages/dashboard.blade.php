@extends('layouts.app')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Manajemen Aset</h1>
    
    <form action="{{ route('dashboard') }}" method="GET" class="d-none d-sm-inline-block">
        <div class="input-group">
            <select name="lokasi" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="">Semua Lokasi</option>
                @foreach ($allLokasi as $lokasi)
                    <option value="{{ $lokasi }}" {{ $selectedLokasi == $lokasi ? 'selected' : '' }}>
                        {{ ucfirst($lokasi) }}
                    </option>
                @endforeach
            </select>
            <div class="input-group-append">
                <button class="btn btn-primary btn-sm" type="submit">
                    <i class="fas fa-filter fa-sm"></i> Filter
                </button>
            </div>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Nilai Aset (KIB A-D)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($kpiTotalNilai, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Unit Aset</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($kpiTotalAset, 0, ',', '.') }} Unit</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-12 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pajak Segera Jatuh Tempo</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($asetPajakMendatang) }} Aset</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Daftar Kendaraan Wajib Pajak (H-7 Hari)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Aset & Pemakai</th>
                                <th>No. Polisi</th>
                                <th>Lokasi</th>
                                <th>Tgl Jatuh Tempo</th>
                                <th>Sisa Hari</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asetPajakMendatang as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->peralatan->nama_barang }}</strong><br>
                                    <small class="text-muted"><i class="fas fa-user fa-sm"></i> {{ $item->pemakai_nama }}</small>
                                </td>
                                <td>{{ $item->peralatan->nomor_polisi ?? '-' }}</td>
                                <td>{{ ucfirst($item->lokasi) }}</td>
                                <td class="font-weight-bold text-danger">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pajak)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @php
                                        $diff = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($item->tanggal_pajak), false);
                                    @endphp
                                    @if($diff < 0)
                                        <span class="badge badge-dark">Terlewat {{ abs($diff) }} Hari</span>
                                    @elseif($diff <= 7)
                                        <span class="badge badge-danger">Kritis: {{ $diff }} Hari lagi</span>
                                    @else
                                        <span class="badge badge-warning text-dark">{{ $diff }} Hari lagi</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->nomor_pemakai)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->nomor_pemakai) }}" target="_blank" class="btn btn-sm btn-success btn-circle" title="Hubungi Pemakai">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $item->lokasi]) }}" class="btn btn-sm btn-info btn-circle">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500">
                                    <i class="fas fa-check-circle text-success mb-2 d-block fa-2x"></i>
                                    Semua administrasi pajak kendaraan aman.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Nilai Aset per Kategori</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="chartNilaiAset"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Komposisi Unit Aset</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="chartKomposisiAset"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tren Perolehan Aset per Tahun</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="chartPerolehan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Akses Cepat Data KIB</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @php $defaultLokasi = $selectedLokasi ?? ($allLokasi->first() ?? 'pusat'); @endphp
                    <a href="{{ route('lokasi.tanah.index', ['lokasi' => $defaultLokasi]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-primary">
                        <span><i class="fas fa-map-marked-alt fa-fw mr-2"></i> KIB A (Tanah)</span>
                        <span class="badge badge-primary badge-pill">{{ $countTanah }}</span>
                    </a>
                    <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $defaultLokasi]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-success">
                        <span><i class="fas fa-tractor fa-fw mr-2"></i> KIB B (Peralatan & Mesin)</span>
                        <span class="badge badge-success badge-pill">{{ $countPeralatan }}</span>
                    </a>
                    <a href="{{ route('lokasi.gedung.index', ['lokasi' => $defaultLokasi]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-info">
                        <span><i class="fas fa-building fa-fw mr-2"></i> KIB C (Gedung & Bangunan)</span>
                        <span class="badge badge-info badge-pill">{{ $countGedung }}</span>
                    </a>
                    <a href="{{ route('lokasi.jalan.index', ['lokasi' => $defaultLokasi]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-warning">
                        <span><i class="fas fa-road fa-fw mr-2"></i> KIB D (Jalan & Jaringan)</span>
                        <span class="badge badge-warning badge-pill text-white">{{ $countJalan }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Konfigurasi Chart.js sama dengan sebelumnya
    // ... (Fungsi number_format dan inisialisasi chart)
</script>
@endpush