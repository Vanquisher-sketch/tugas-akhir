<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aset - {{ $detail->dt_alat_kode_barcode }}</title>
    
    {{-- Memanggil Bootstrap & FontAwesome langsung dari CDN Internet --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Nunito', sans-serif;
        }
        .img-aset {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        .empty-foto {
            width: 100%;
            height: 200px;
            border: 2px dashed #cbd5e1;
            border-radius: 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            {{-- KARTU INFORMASI ASET --}}
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4 rounded-top">
                    <i class="fas fa-qrcode fa-3x mb-2 text-white-50"></i>
                    <h5 class="m-0 font-weight-bold">Informasi Aset Fisik</h5>
                    <p class="m-0 text-white-50 small">Kecamatan {{ ucfirst($lokasi) }}</p>
                </div>
                
                <div class="card-body p-4 text-dark">
                    
                    {{-- 🌟 BAGIAN FOTO ASET (SUDAH DIREVISI LOGIKANYA) --}}
                    <div class="mb-4 text-center">
                        @if($detail->dt_alat_foto)
                            {{-- 1. Jika ada foto spesifik fisiknya --}}
                            <img src="{{ asset('storage/' . $detail->dt_alat_foto) }}" alt="Foto Aset {{ $detail->dt_alat_kode_barcode }}" class="img-aset shadow-sm">
                        @elseif($peralatan->alat_foto)
                            {{-- 2. Jika fisik kosong, ambil foto dari data Induk --}}
                            <img src="{{ asset('storage/' . $peralatan->alat_foto) }}" alt="Foto Induk {{ $peralatan->alat_nama_barang }}" class="img-aset shadow-sm">
                        @else
                            {{-- 3. Jika Induk dan Fisik sama-sama tidak punya foto --}}
                            <div class="empty-foto text-muted shadow-sm">
                                <i class="fas fa-camera fa-3x mb-2 text-light"></i>
                                <span class="small font-weight-bold">Belum Ada Foto</span>
                            </div>
                        @endif
                    </div>

                    {{-- Status Tag Barcode --}}
                    <div class="text-center mb-4">
                        <h3 class="font-weight-bold text-primary mb-2">{{ $detail->dt_alat_kode_barcode }}</h3>
                        
                        @if($detail->dt_alat_kondisi === 'Baik')
                            <span class="badge badge-success px-3 py-2" style="font-size: 14px;"><i class="fas fa-check-circle mr-1"></i>Kondisi Baik</span>
                        @elseif($detail->dt_alat_kondisi === 'Rusak Ringan')
                            <span class="badge badge-warning text-dark px-3 py-2" style="font-size: 14px;"><i class="fas fa-exclamation-triangle mr-1"></i>Rusak Ringan</span>
                        @else
                            <span class="badge badge-danger px-3 py-2" style="font-size: 14px;"><i class="fas fa-times-circle mr-1"></i>Rusak Berat</span>
                        @endif
                    </div>

                    <hr>

                    {{-- Detail List --}}
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Nama Barang (Induk)</span>
                            <strong class="text-right">{{ $peralatan->alat_nama_barang }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Merk / Tipe</span>
                            <strong class="text-right">{{ $peralatan->alat_merk_tipe ?? '-' }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Lokasi Ruangan</span>
                            <strong class="text-right">{{ $peralatan->alat_lokasi_fisik ?? '-' }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Tahun Perolehan</span>
                            <strong class="text-right">{{ $peralatan->alat_tanggal_perolehan ? \Carbon\Carbon::parse($peralatan->alat_tanggal_perolehan)->format('Y') : '-' }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Status Ketersediaan</span>
                            @if($detail->dt_alat_status_pinjam === 'Tersedia')
                                <strong class="text-success"><i class="fas fa-check mr-1"></i> Tersedia</strong>
                            @else
                                <strong class="text-info"><i class="fas fa-hand-holding mr-1"></i> Sedang Dipinjam</strong>
                            @endif
                        </li>
                        
                        {{-- 🌟 BAGIAN KETERANGAN --}}
                        <li class="list-group-item px-0 flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <span class="text-muted small">Keterangan Tambahan</span>
                            </div>
                            <p class="mb-0 text-dark font-weight-bold">{{ $detail->dt_alat_keterangan ?? '-' }}</p>
                        </li>
                    </ul>

                    {{-- Alert Tanggal Pengecekan --}}
                    <div class="alert alert-secondary border-0 bg-light text-center mb-0 shadow-sm" style="font-size: 13px;">
                        <i class="fas fa-history text-muted mr-1"></i> Terakhir dicek secara fisik pada:<br>
                        <strong>{{ $detail->dt_alat_tanggal_cek ? \Carbon\Carbon::parse($detail->dt_alat_tanggal_cek)->format('d F Y') : 'Belum pernah dicek' }}</strong>
                    </div>
                </div>

                <div class="card-footer text-center bg-white border-top-0 pb-4 text-muted small">
                    &copy; 2026 Sistem Inventaris Pandawa<br>Kecamatan {{ ucfirst($lokasi) }}
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>