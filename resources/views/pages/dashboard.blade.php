@extends('layouts.app')

@section('content')
<div class="container-fluid text-dark">
    {{-- Header Dashboard & Dropdown Filter Lokasi --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-fw fa-tachometer-alt mr-2 text-primary"></i>Panel Kontrol Utama PANDAWA</h1>
        
        {{-- Filter Lokasi Dinamis --}}
        <form action="{{ route('dashboard') }}" method="GET" class="form-inline">
            <div class="form-group mb-0">
                <label class="mr-2 font-weight-bold text-gray-700" style="font-size: 13px;">Filter Wilayah Kerja:</label>
                <select name="lokasi" class="form-control form-control-sm font-weight-bold text-dark border-primary shadow-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Wilayah / Kecamatan --</option>
                    @foreach($allLokasi as $lok)
                        <option value="{{ $lok }}" {{ $selectedLokasi == $lok ? 'selected' : '' }}>
                            {{ ucfirst($lok) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- BARIS KARTU KPI UTAMA --}}
    <div class="row">
        {{-- Total Volume Kuantitas Aset --}}
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Volume Manifes KIB (Global)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-900">{{ number_format($kpiTotalAset) }} <span style="font-size: 13px;" class="text-muted font-weight-normal">Register Komponen</span></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Kapitalisasi Nilai Rupiah Aset --}}
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Nilai Kapitalisasi Aset</div>
                            <div class="h5 mb-0 font-weight-bold text-success">Rp {{ number_format($kpiTotalNilai, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS DISTRIBUSI KELOMPOK BARANG --}}
    <div class="row">
        {{-- KIB A --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-bottom-dark shadow-sm h-100 bg-white p-2">
                <div class="card-body p-2 text-center">
                    <small class="font-weight-bold text-muted text-uppercase d-block mb-1">KIB A (Tanah)</small>
                    <h4 class="font-weight-bold text-dark mb-0">{{ $countTanah }} <small style="font-size: 11px;">Berkas</small></h4>
                </div>
            </div>
        </div>
        {{-- KIB B --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-bottom-primary shadow-sm h-100 bg-white p-2">
                <div class="card-body p-2 text-center">
                    <small class="font-weight-bold text-muted text-uppercase d-block mb-1">KIB B (Peralatan)</small>
                    <h4 class="font-weight-bold text-primary mb-0">{{ $countPeralatan }} <small style="font-size: 11px;">Unit</small></h4>
                </div>
            </div>
        </div>
        {{-- KIB C --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-bottom-info shadow-sm h-100 bg-white p-2">
                <div class="card-body p-2 text-center">
                    <small class="font-weight-bold text-muted text-uppercase d-block mb-1">KIB C (Gedung)</small>
                    <h4 class="font-weight-bold text-info mb-0">{{ $countGedung }} <small style="font-size: 11px;">Bangunan</small></h4>
                </div>
            </div>
        </div>
        {{-- KIB D --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-bottom-warning shadow-sm h-100 bg-white p-2">
                <div class="card-body p-2 text-center">
                    <small class="font-weight-bold text-muted text-uppercase d-block mb-1">KIB D (Jalan)</small>
                    <h4 class="font-weight-bold text-warning mb-0">{{ $countJalan }} <small style="font-size: 11px;">Ruas</small></h4>
                </div>
            </div>
        </div>
    </div>

    {{-- KONTEN MONITORING JURNAL PAJAK KENDARAAN (DARI PROYEK DATA) --}}
    <div class="card shadow mb-4 bg-white">
        <div class="card-header py-3 bg-white border-bottom border-light">
            <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-bell mr-2"></i>Pemberitahuan Pajak Jatuh Tempo (KIB B Kendaraan Dinas)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 text-dark font-weight-bold" style="font-size: 12px; vertical-align: middle;">
                    <thead class="bg-gray-100 text-center text-uppercase" style="font-size: 11px;">
                        <tr>
                            <th class="py-3">No</th>
                            <th>No. Registrasi No Plat</th>
                            <th>Wilayah Kerja</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Status Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asetPajakMendatang as $pajak)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-primary font-weight-bold text-center">{{ $pajak->no_regristrasi }}</td>
                            <td class="text-center text-uppercase"><span class="badge badge-dark">{{ $pajak->lokasi }}</span></td>
                            <td class="text-center font-weight-bold text-danger">{{ \Carbon\Carbon::parse($pajak->tanggal_pajak)->translatedFormat('d F Y') }}</td>
                            <td class="text-center">
                                @if(\Carbon\Carbon::parse($pajak->tanggal_pajak)->isPast())
                                    <span class="badge badge-danger px-2 py-1 font-weight-bold text-uppercase"><i class="fas fa-times-circle mr-1"></i> Terlewat Tempo</span>
                                @else
                                    <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold text-uppercase"><i class="fas fa-exclamation-triangle mr-1"></i> Wajib Reminder</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted font-weight-bold">
                                <i class="fas fa-check-circle text-success fa-lg mr-1"></i> Seluruh urusan administrasi pajak bmd aman dalam 30 hari ke depan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection