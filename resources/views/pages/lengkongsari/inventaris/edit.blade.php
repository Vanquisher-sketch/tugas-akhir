@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit mr-2"></i> Laporkan Perubahan Kondisi Fisik Barang - {{ $room->ruangan_nama }}
        </h6>
    </div>
    <div class="card-body">
        
        {{-- Notifikasi Error Validasi Jika Ada --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal Memperbarui Data:</h6>
                <ul class="mb-0 pl-3" style="font-size: 12px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <form action="{{ route('lokasi.inventaris.update', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan, 'inv_kode_barang' => $inventari->inv_kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Informasi Baris Aset Saat Ini --}}
            <div class="alert alert-info shadow-sm border-left-info">
                <h5 class="font-weight-bold text-info" style="font-size: 14px;"><i class="fas fa-info-circle mr-1"></i> Manifes Data Aset Saat Ini:</h5>
                <div class="row text-dark mt-2" style="font-size: 13px;">
                    <div class="col-md-6">
                        <p class="mb-1">Nama Barang: <b>{{ optional($inventari->peralatan)->alat_nama_barang ?? $inventari->inv_nama_barang }}</b></p>
                        <p class="mb-1">Kode Barang: <b class="text-primary">{{ $inventari->inv_kode_barang }}</b></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1">Stok di Ruangan: <b>{{ $inventari->inv_jumlah }} {{ $inventari->inv_satuan }}</b></p>
                        <p class="mb-0">Kondisi Saat Ini: <span class="badge badge-dark font-weight-bold">{{ $inventari->inv_kondisi }}</span></p>
                    </div>
                </div>
            </div>

            {{-- Form Cerdar Pecah Kuantitas Kondisi --}}
            <div class="row bg-light p-3 rounded border my-4">
                <div class="col-md-6 form-group mb-md-0">
                    <label class="font-weight-bold text-danger">Jumlah Unit Yang Berubah Kondisi: <span class="text-danger">*</span></label>
                    {{-- Input dibatasi maksimal senilai jumlah barang yang ada di ruangan --}}
                    <input type="number" 
                           name="qty_ubah" 
                           class="form-control font-weight-bold text-dark" 
                           value="{{ old('qty_ubah', 1) }}" 
                           min="1" 
                           max="{{ $inventari->inv_jumlah }}" 
                           required 
                           style="border: 2px solid #e74a3b;">
                    <small class="form-text text-muted font-weight-bold mt-2 text-secondary">
                        * Contoh: Jika dari {{ $inventari->inv_jumlah }} unit ada 4 unit yang rusak, isi angka 4.
                    </small>
                </div>

                <div class="col-md-6 form-group mb-0">
                    <label class="font-weight-bold text-danger">Ubah Kondisi Unit Tersebut Menjadi: <span class="text-danger">*</span></label>
                    <select name="kondisi_baru" class="form-control font-weight-bold text-dark" required style="border: 2px solid #e74a3b;">
                        <option value="Baik" {{ $inventari->inv_kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ $inventari->inv_kondisi == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ $inventari->inv_kondisi == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>
            </div>

            {{-- Kolom Atribut Tambahan (Readonly / Kunci agar tidak merusak relasi BMD) --}}
            <div class="row mt-2">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted">NIBAR (Terkunci)</label>
                    <input type="text" class="form-control bg-light text-muted" value="{{ $inventari->inv_nibar ?? '-' }}" readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted">Nomor Register (Terkunci)</label>
                    <input type="text" class="form-control bg-light text-muted" value="{{ $inventari->inv_nomor_register ?? '-' }}" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark">Catatan / Keterangan Kerusakan Barang</label>
                <textarea name="keterangan" class="form-control text-dark" rows="3" placeholder="Contoh: 4 unit kursi mengalami patah pada sandaran tangan sebelah kanan akibat pemakaian operasional...">{{ old('keterangan', $inventari->inv_keterangan) }}</textarea>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}" class="btn btn-secondary mr-2 font-weight-bold">Batal</a>
                <button type="submit" class="btn btn-warning font-weight-bold text-white"><i class="fas fa-hammer mr-1"></i> Eksekusi Perubahan Kondisi</button>
            </div>
        </form>
    </div>
</div>
@endsection