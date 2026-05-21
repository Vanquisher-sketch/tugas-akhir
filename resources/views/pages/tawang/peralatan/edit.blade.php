@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Peralatan & Mesin - [{{ $peralatan->kode_barang }}]</h6>
        <a href="{{ route('lokasi.peralatan.index', $lokasi) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>
    <div class="card-body" style="color: #000;">
        <form action="{{ route('lokasi.peralatan.update', [$lokasi, $peralatan->kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Bagian Kiri --}}
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3"><i class="fas fa-boxes mr-2 text-primary"></i>1. Identitas & Spesifikasi Aset</h5>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" class="form-control" value="{{ old('kode_barang', $peralatan->kode_barang) }}" readonly required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nomor Register <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_register" class="form-control" value="{{ old('nomor_register', $peralatan->nomor_register) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $peralatan->nama_barang) }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Merk / Tipe</label>
                            <input type="text" name="merk_tipe" class="form-control" value="{{ old('merk_tipe', $peralatan->merk_tipe) }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">NIBR / NIBAR</label>
                            <input type="text" name="nibr" class="form-control" value="{{ old('nibr', $peralatan->nibr) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi Ruang / Tempat Fisik <span class="text-danger">*</span></label>
                        <input type="text" name="Lok" class="form-control" value="{{ old('Lok', $peralatan->Lok) }}" required>
                    </div>

                    {{-- 🌟 EDIT KONDISI BARANG (Poin 6) --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Kondisi Fisik Barang <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-control" required>
                            <option value="Baik" {{ old('kondisi', $peralatan->kondisi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ old('kondisi', $peralatan->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('kondisi', $peralatan->kondisi) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Spesifikasi Lainnya</label>
                        <textarea name="spesifikasi_lainnya" class="form-control" rows="2">{{ old('spesifikasi_lainnya', $peralatan->spesifikasi_lainnya) }}</textarea>
                    </div>
                </div>

                {{-- Bagian Kanan --}}
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3"><i class="fas fa-car mr-2 text-primary"></i>2. Atribut Kendaraan Dinas & Legalitas (Poin 4)</h5>
                    
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Nomor Polisi</label>
                            <input type="text" name="nomor_polisi" class="form-control" value="{{ old('nomor_polisi', $peralatan->nomor_polisi) }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Tgl Pajak Tahunan</label>
                            <input type="date" name="tanggal_pajak" class="form-control" value="{{ old('tanggal_pajak', $peralatan->tanggal_pajak) }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Tgl STNK (5 Tahunan)</label>
                            <input type="date" name="tanggal_stnk" class="form-control" value="{{ old('tanggal_stnk', $peralatan->tanggal_stnk) }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nomor Rangka</label>
                            <input type="text" name="nomor_rangka" class="form-control" value="{{ old('nomor_rangka', $peralatan->nomor_rangka) }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nomor BPKB</label>
                            <input type="text" name="nomor_bpkb" class="form-control" value="{{ old('nomor_bpkb', $peralatan->nomor_bpkb) }}">
                        </div>
                    </div>

                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3 mt-4"><i class="fas fa-coins mr-2 text-primary"></i>3. Nilai Perolehan & Pengadaan</h5>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Jumlah Barang <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah', $peralatan->jumlah) }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $peralatan->satuan) }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Cara Perolehan <span class="text-danger">*</span></label>
                            <input type="text" name="cara_perolehan" class="form-control" value="{{ old('cara_perolehan', $peralatan->cara_perolehan) }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="harga_satuan" class="form-control currency-input" value="{{ old('harga_satuan', number_format($peralatan->harga_satuan, 0, ',', '.')) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nilai Perolehan total (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="nilai_perolehan" class="form-control currency-input" value="{{ old('nilai_perolehan', number_format($peralatan->nilai_perolehan, 0, ',', '.')) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal Perolehan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_perolehan" class="form-control" value="{{ old('tanggal_perolehan', $peralatan->tanggal_perolehan) }}" required>
                    </div>

                    {{-- Pertahankan Status Penggunaan yang diubah secara otomatis oleh sistem --}}
                    <input type="hidden" name="status_penggunaan" value="{{ $peralatan->status_penggunaan }}">

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $peralatan->keterangan) }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('lokasi.peralatan.index', $lokasi) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-warning text-dark px-5 shadow-sm font-weight-bold">
                    <i class="fas fa-save mr-1"></i> Perbarui Data Peralatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection