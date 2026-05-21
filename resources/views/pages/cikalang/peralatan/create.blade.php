@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Peralatan & Mesin (KIB B)</h6>
        <a href="{{ route('lokasi.peralatan.index', $lokasi) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>
    <div class="card-body" style="color: #000;">
        <form action="{{ route('lokasi.peralatan.store', $lokasi) }}" method="POST">
            @csrf

            <div class="row">
                {{-- Bagian Kiri: Spesifikasi Aset --}}
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3"><i class="fas fa-boxes mr-2 text-primary"></i>1. Identitas & Spesifikasi Aset</h5>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" value="{{ old('kode_barang') }}" placeholder="Contoh: 1.3.2.05.001" required>
                            @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nomor Register <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_register" class="form-control @error('nomor_register') is-invalid @enderror" value="{{ old('nomor_register') }}" placeholder="Contoh: 0001" required>
                            @error('nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang') }}" placeholder="Contoh: Sepeda Motor Honda Vario 160" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Merk / Tipe</label>
                            <input type="text" name="merk_tipe" class="form-control" value="{{ old('merk_tipe') }}" placeholder="Contoh: Honda / Vario 160">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">NIBR / NIBAR</label>
                            <input type="text" name="nibr" class="form-control" value="{{ old('nibr') }}" placeholder="Nomor Induk Barang">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi Ruang / Tempat Fisik <span class="text-danger">*</span></label>
                        <input type="text" name="Lok" class="form-control @error('Lok') is-invalid @enderror" value="{{ old('Lok') }}" placeholder="Contoh: Ruang Subag Keuangan" required>
                        @error('Lok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Kondisi Fisik Barang <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-control" required>
                            <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Spesifikasi Lainnya</label>
                        <textarea name="spesifikasi_lainnya" class="form-control" rows="2" placeholder="Keterangan tambahan spesifikasi..."></textarea>
                    </div>
                </div>

                {{-- Bagian Kanan: Legalitas, Keuangan & Kendaraan Dinas --}}
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3"><i class="fas fa-car mr-2 text-primary"></i>2. Atribut Kendaraan Dinas & Legalitas (Poin 4)</h5>
                    
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Nomor Polisi</label>
                            <input type="text" name="nomor_polisi" class="form-control" value="{{ old('nomor_polisi') }}" placeholder="Z 1234 X">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Tgl Pajak Tahunan</label>
                            <input type="date" name="tanggal_pajak" class="form-control" value="{{ old('tanggal_pajak') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Tgl STNK (5 Tahunan)</label>
                            <input type="date" name="tanggal_stnk" class="form-control" value="{{ old('tanggal_stnk') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nomor Rangka</label>
                            <input type="text" name="nomor_rangka" class="form-control" value="{{ old('nomor_rangka') }}" placeholder="Masukkan No. Rangka">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nomor BPKB</label>
                            <input type="text" name="nomor_bpkb" class="form-control" value="{{ old('nomor_bpkb') }}" placeholder="Masukkan No. BPKB">
                        </div>
                    </div>

                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3 mt-4"><i class="fas fa-coins mr-2 text-primary"></i>3. Nilai Perolehan & Pengadaan</h5>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Jumlah Barang <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', 1) }}" required>
                            @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control @error('satuan') is-invalid @enderror" value="{{ old('satuan', 'Unit') }}" placeholder="Contoh: Unit, Buah" required>
                            @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Cara Perolehan <span class="text-danger">*</span></label>
                            <input type="text" name="cara_perolehan" class="form-control @error('cara_perolehan') is-invalid @enderror" value="{{ old('cara_perolehan', 'Pembelian') }}" required>
                            @error('cara_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="harga_satuan" class="form-control currency-input @error('harga_satuan') is-invalid @enderror" value="{{ old('harga_satuan') }}" placeholder="0" required>
                            @error('harga_satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nilai Perolehan total (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="nilai_perolehan" class="form-control currency-input @error('nilai_perolehan') is-invalid @enderror" value="{{ old('nilai_perolehan') }}" placeholder="0" required>
                            @error('nilai_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal Perolehan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_perolehan" class="form-control @error('tanggal_perolehan') is-invalid @enderror" value="{{ old('tanggal_perolehan', date('Y-m-d')) }}" required>
                        @error('tanggal_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Set Default Status Penggunaan secara tersembunyi karena otomatis diatur oleh sistem --}}
                    <input type="hidden" name="status_penggunaan" value="Tidak Aktif">

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan opsional pengadaan..."></textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-4">
                <button type="reset" class="btn btn-secondary mr-2">Reset</button>
                <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold">
                    <i class="fas fa-save mr-1"></i> Simpan Data Peralatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection