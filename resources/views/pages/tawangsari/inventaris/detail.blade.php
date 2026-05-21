@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Topbar Menu Navigasi --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-barcode text-primary mr-2"></i>Manifes Detail Unit Satuan
        </h1>
        <div>
            <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Ruangan
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-success shadow-sm font-weight-bold">
                <i class="fas fa-print fa-sm mr-1"></i> Mulai Cetak Label Fisik
            </button>
        </div>
    </div>

    {{-- Ringkasan Induk Aset --}}
    <div class="card shadow-sm mb-4 text-dark border-left-primary d-print-none">
        <div class="card-body bg-light py-3 d-flex align-items-center justify-content-between" style="font-size: 13px;">
            <div>
                <span class="font-weight-bold text-primary"><i class="fas fa-info-circle mr-1"></i> Kelompok Induk:</span> 
                <strong class="text-gray-900 ml-1">{{ $item->nama_barang }}</strong> ({{ $item->kode_barang }})
            </div>
            <div>
                Volume Logistik: <span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size: 11px;">{{ $item->jumlah }} {{ $item->satuan }}</span>
            </div>
        </div>
    </div>

    {{-- 1. TABEL DATA DETAIL (PERSIS ACUAN PROYEKTOR DOSEN) --}}
    <div class="card shadow-sm mb-4 d-print-none">
        <div class="card-header py-3 bg-white border-bottom">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-database mr-2 text-secondary"></i>Struktur Record Data `detail_inventaris`</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-dark font-weight-bold mb-0" width="100%" cellspacing="0" style="font-size: 12px; vertical-align: middle;">
                    <thead class="bg-gray-100 text-center text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3">id_detail1 (PK)</th>
                            <th>id_barang (FK)</th>
                            <th>kode_barcode</th>
                            <th>kondisi</th>
                            <th>lokasi</th>
                            <th>status_pinjam</th>
                            <th>tanggal_cek</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unitPecahan as $unit)
                        <tr>
                            <td class="text-center font-weight-bold text-primary bg-light" style="width: 14%;">#DTL-{{ $unit->id_detail1 }}</td>
                            <td class="text-center text-secondary" style="font-size: 11px;">{{ $unit->id_barang }}</td>
                            <td class="text-center text-danger font-weight-bold" style="font-family: 'Courier New', monospace; font-size: 13px; width: 18%;">{{ $unit->kode_barcode }}</td>
                            <td class="text-center" style="width: 12%;">
                                @if($unit->kondisi === 'Baik')
                                    <span class="badge badge-success px-2 py-1 text-uppercase" style="font-size: 9px;"><i class="fas fa-check mr-1"></i>Baik</span>
                                @elseif($unit->kondisi === 'Rusak Ringan')
                                    <span class="badge badge-warning text-dark px-2 py-1 text-uppercase" style="font-size: 9px;"><i class="fas fa-exclamation mr-1"></i>Rusak Rgn</span>
                                @else
                                    <span class="badge badge-danger px-2 py-1 text-uppercase" style="font-size: 9px;"><i class="fas fa-times mr-1"></i>Rusak Brt</span>
                                @endif
                            </td>
                            <td class="pl-3">{{ $unit->lokasi }}</td>
                            <td class="text-center" style="width: 12%;">
                                <span class="badge badge-pill badge-light border border-info text-info px-2 py-1 text-uppercase" style="font-size: 9px;">{{ $unit->status_pinjam }}</span>
                            </td>
                            <td class="text-center text-muted" style="font-size: 11px; width: 12%;">{{ \Carbon\Carbon::parse($unit->tanggal_cek)->translatedFormat('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted font-weight-bold">
                                <i class="fas fa-exclamation-circle fa-2x mb-2 d-block text-gray-400"></i>
                                Data pecahan detail belum digenerate di database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr class="d-print-none my-4">

    {{-- 2. TAMPILAN KARTU LABEL FISIK + QR CODE UNTUK SIAP DICETAK --}}
    <h5 class="font-weight-bold text-dark mb-3 d-print-none"><i class="fas fa-qrcode mr-2 text-primary"></i>Preview Cetak Stiker Label Fisik</h5>
    
    {{-- Grid Label Container --}}
    <div class="row-print-layout">
        @foreach($unitPecahan as $unit)
        <div class="card-label-box">
            <div class="inner-label-border">
                {{-- Kop Pemerintah Daerah --}}
                <div class="label-header-bmd">
                    <i class="fas fa-landmark mr-1"></i> Kota Tasikmalaya - KEC. TAWANG
                </div>
                
                {{-- Konten Utama Label --}}
                <div class="label-body-content">
                    <div class="label-qr-section">
                        <div class="qr-wrapper-box">
                            {!! QrCode::size(80)->margin(0)->generate($unit->kode_barcode) !!}
                        </div>
                        <div class="unit-index-tag">
                            UNIT #{{ $loop->iteration }}
                        </div>
                    </div>
                    
                    <div class="label-info-section">
                        <div class="asset-title-text">
                            {{ $item->nama_barang }}
                        </div>
                        <div class="asset-barcode-text">
                            *{{ $unit->kode_barcode }}*
                        </div>
                        <div class="asset-meta-details">
                            <table>
                                <tr>
                                    <td>Kondisi</td>
                                    <td>: <strong>{{ $unit->kondisi }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Ruangan</td>
                                    <td>: <span class="text-primary-dark font-weight-bold">{{ $unit->lokasi }}</span></td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>: <span class="text-success-dark font-weight-bold">{{ $unit->status_pinjam }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- CSS PENYELARAS MODAL / STRUKTUR TAMPILAN WEB & CETAK --}}
<style>
/* 🖥️ CSS UNTUK TAMPILAN SCREEN MONITOR LAYAR */
.row-print-layout {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    width: 100%;
}
.card-label-box {
    background: #ffffff;
    border: 1px solid #d1d3e2;
    border-radius: 6px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
    padding: 10px;
    transition: transform 0.2s;
}
.card-label-box:hover {
    transform: translateY(-2px);
}
.inner-label-border {
    border: 2px dashed #4e73df;
    border-radius: 4px;
    overflow: hidden;
    background: #fff;
}
.label-header-bmd {
    background: #4e73df;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    text-align: center;
    padding: 6px 4px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border-bottom: 2px dashed #4e73df;
}
.label-body-content {
    display: flex;
    padding: 12px 10px;
    align-items: center;
}
.label-qr-section {
    flex: 0 0 35%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-right: 1px dashed #e3e6f0;
    padding-right: 10px;
}
.qr-wrapper-box {
    padding: 4px;
    background: #fff;
    border: 1px solid #dddfeb;
    border-radius: 4px;
}
.unit-index-tag {
    margin-top: 6px;
    background: #5a5c69;
    color: #fff;
    font-size: 8px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 10px;
    text-align: center;
}
.label-info-section {
    flex: 0 0 65%;
    padding-left: 12px;
}
.asset-title-text {
    font-size: 13px;
    font-weight: 800;
    color: #1a202c;
    line-height: 1.2;
    margin-bottom: 2px;
    text-transform: uppercase;
}
.asset-barcode-text {
    font-family: 'Courier New', Courier, monospace;
    font-size: 10px;
    font-weight: 700;
    color: #e74a3b;
    margin-bottom: 6px;
}
.asset-meta-details table {
    width: 100%;
    font-size: 10px;
    color: #4e5154;
    line-height: 1.3;
}
.asset-meta-details table td {
    padding: 1px 0;
    vertical-align: top;
}
.text-primary-dark { color: #2e59d9; }
.text-success-dark { color: #1cc88a; }

/* 🖨️ CSS SAKTI KHUSUS MODE CETAK PRINTER (AUTO-LURUS NO DEBAT) */
@media print {
    /* Sembunyikan semua elemen navigasi admin dashboard */
    #accordionSidebar, .navbar, .d-print-none, footer, .alert, hr {
        display: none !important;
    }
    body {
        background-color: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .content-wrapper, .container-fluid, #content {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    /* Ganti grid monitor jadi float print CSS standar */
    .row-print-layout {
        display: block !important;
        width: 100% !important;
    }
    .card-label-box {
        width: 31% !important; /* Pas buat lipatan 3 kolom label stiker */
        float: left !important;
        margin: 1% !important;
        box-shadow: none !important;
        border: 1px solid #000000 !important;
        page-break-inside: avoid !important; /* Mencegah label terpotong di tengah halaman */
    }
    .inner-label-border {
        border: 1px dashed #000000 !important;
    }
    .label-header-bmd {
        background: #000000 !important;
        color: #ffffff !important;
        border-bottom: 1px dashed #000000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .unit-index-tag {
        background: #000000 !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endsection