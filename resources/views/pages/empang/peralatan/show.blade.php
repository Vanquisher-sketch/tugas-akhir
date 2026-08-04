@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list mr-2"></i>Detail Item Peralatan - {{ ucfirst($lokasi) }}
        </h6>
        <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Induk
        </a>
    </div>

    <div class="card-body">
        {{-- KOTAK INFORMASI INDUK (KIB B) --}}
        <div class="alert alert-info border-left-info shadow-sm mb-4">
            <h6 class="font-weight-bold text-info border-bottom pb-2 mb-3"><i class="fas fa-boxes mr-1"></i> Informasi Induk (KIB B)</h6>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless text-dark mb-0">
                        <tr><th width="35%">Kode Barang</th><td>: <strong>{{ $peralatan->alat_kode_barang }}</strong></td></tr>
                        <tr><th>Nama Barang</th><td>: {{ $peralatan->alat_nama_barang }}</td></tr>
                        <tr><th>Merk/Tipe</th><td>: {{ $peralatan->alat_merk_tipe ?? '-' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless text-dark mb-0">
                        <tr><th width="40%">Total Jumlah Induk</th><td>: <span class="badge badge-primary px-2">{{ $peralatan->alat_jumlah }} {{ $peralatan->alat_satuan }}</span></td></tr>
                        <tr><th>Lokasi Fisik</th><td>: {{ $peralatan->alat_lokasi_fisik ?? '-' }}</td></tr>
                        <tr><th>Tgl Perolehan</th><td>: {{ $peralatan->alat_tanggal_perolehan ? \Carbon\Carbon::parse($peralatan->alat_tanggal_perolehan)->format('d/m/Y') : '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- BAGIAN TABEL DETAIL PERALATAN (OTOMATIS) --}}
        <div class="mb-3">
            <h5 class="m-0 font-weight-bold text-dark"><i class="fas fa-barcode mr-2"></i>Daftar Fisik & Barcode ({{ $detailPeralatan->count() }} Item)</h5>
            <small class="text-muted">Data barcode fisik di-generate otomatis oleh sistem sesuai dengan Total Jumlah Induk.</small>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-dark text-center" width="100%" cellspacing="0" style="font-size: 13px; vertical-align: middle;">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="8%">Foto</th> {{-- 🌟 Kolom Foto Baru --}}
                        <th>Kode Barcode</th>
                        <th>Kondisi Fisik</th>
                        <th>Status Peminjaman</th>
                        <th>Keterangan</th> {{-- 🌟 Kolom Keterangan Baru --}}
                        <th>Terakhir Dicek</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($detailPeralatan as $detail)
                    <tr>
                        <td class="align-middle">{{ $loop->iteration }}</td>
                        
                        {{-- 🌟 BAGIAN TAMPILAN FOTO 🌟 --}}
                        {{-- 🌟 BAGIAN TAMPILAN FOTO 🌟 --}}
<td class="align-middle">
    @if($detail->dt_alat_foto)
        {{-- 1. Jika item fisik sudah diupload foto spesifiknya via edit --}}
        <img src="{{ asset('storage/' . $detail->dt_alat_foto) }}" alt="Foto Fisik" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
    @elseif($peralatan->alat_foto)
        {{-- 2. Jika fisik belum punya foto, ambil dari data Induk (KIB B) --}}
        <img src="{{ asset('storage/' . $peralatan->alat_foto) }}" alt="Foto Induk" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" title="Foto dari Data Induk">
    @else
        {{-- 3. Jika induk dan fisik sama-sama tidak punya foto --}}
        <span class="text-muted small">
            <i class="fas fa-image fa-2x mb-1 text-light"></i><br>No Photo
        </span>
    @endif
</td>

                        {{-- 🌟 BAGIAN QR CODE --}}
                        <td class="align-middle text-center py-2">
                            <div class="bg-white d-inline-block p-1 border rounded shadow-sm mb-1">
                                {!! QrCode::size(50)->generate(route('lokasi.peralatan.scan', ['lokasi' => $lokasi, 'barcode' => $detail->dt_alat_kode_barcode])) !!}
                            </div>
                            <br>
                            <span class="font-weight-bold text-primary" style="font-size: 12px;">{{ $detail->dt_alat_kode_barcode }}</span>
                        </td>

                        <td class="align-middle">
                            @if($detail->dt_alat_kondisi === 'Baik')
                                <span class="badge badge-success px-2 py-1">Baik</span>
                            @elseif($detail->dt_alat_kondisi === 'Rusak Ringan')
                                <span class="badge badge-warning text-dark px-2 py-1">Rusak Rgn</span>
                            @else
                                <span class="badge badge-danger px-2 py-1">Rusak Brt</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($detail->dt_alat_status_pinjam === 'Tersedia')
                                <span class="badge badge-light border text-muted px-2 py-1"><i class="fas fa-check mr-1"></i> Tersedia</span>
                            @else
                                <span class="badge badge-info px-2 py-1"><i class="fas fa-hand-holding mr-1"></i> Dipinjam</span>
                            @endif
                        </td>
                        
                        {{-- 🌟 BAGIAN KETERANGAN --}}
                        <td class="align-middle">
                            {{ $detail->dt_alat_keterangan ?? '-' }}
                        </td>

                        <td class="align-middle">{{ $detail->dt_alat_tanggal_cek ? \Carbon\Carbon::parse($detail->dt_alat_tanggal_cek)->format('d M Y') : 'Belum Pernah' }}</td>
                        <td class="align-middle">
                            {{-- Hanya Aksi Edit (untuk update kondisi/status pinjam) --}}
                            <button class="btn btn-sm btn-warning py-0" title="Edit Kondisi"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- 🌟 Colspan diubah menjadi 8 karena ada tambahan 2 kolom baru --}}
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>
                            Menyiapkan data fisik...
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection