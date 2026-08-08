@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Data KIB B (Peralatan & Mesin) - {{ ucfirst($lokasi) }}
        </h6>
        <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.peralatan.store', ['lokasi' => $lokasi]) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 1. INFORMASI UTAMA BARANG --}}
            <h6 class="font-weight-bold text-primary mb-3 border-bottom pb-2">
                <i class="fas fa-barcode mr-1"></i> Informasi Utama Barang
            </h6>
            
            <div class="row">
                <!-- Tampilan Kode Barang (Alert Box Persis KIB A) -->
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Kode Barang</label>
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-0 p-2" style="background-color: #e0f2fe; color: #0369a1; border-radius: 8px; min-height: 48px;">
                        <small><i class="fas fa-info-circle mr-1"></i> Kode barang akan <strong>dibuat secara otomatis</strong> oleh sistem saat disimpan.</small>
                    </div>
                </div>

                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang') }}" placeholder="Contoh: Laptop / Mesin Tik" required>
                    @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">No. Register</label>
                    <input type="text" name="nomor_register" class="form-control @error('nomor_register') is-invalid @enderror" value="{{ old('nomor_register') }}" placeholder="0001">
                    @error('nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Spesifikasi Barang (Deskripsi)</label>
                    <textarea name="spesifikasi_barang" class="form-control @error('spesifikasi_barang') is-invalid @enderror" rows="2" placeholder="Misal: Laptop Core i5 RAM 8GB">{{ old('spesifikasi_barang') }}</textarea>
                    @error('spesifikasi_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Spesifikasi Lainnya</label>
                    <textarea name="spesifikasi_lainnya" class="form-control @error('spesifikasi_lainnya') is-invalid @enderror" rows="2" placeholder="Informasi tambahan spesifikasi">{{ old('spesifikasi_lainnya') }}</textarea>
                    @error('spesifikasi_lainnya') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- 2. SPESIFIKASI KENDARAAN (OPSIONAL) --}}
            <h6 class="font-weight-bold text-secondary mt-3 mb-3 border-bottom pb-2">2. Legalitas & Kendaraan (Opsional)</h6>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Merk / Tipe</label>
                    <input type="text" name="merk_tipe" class="form-control @error('merk_tipe') is-invalid @enderror" value="{{ old('merk_tipe') }}" placeholder="Contoh: Honda / Civic">
                    @error('merk_tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Nomor Rangka</label>
                    <input type="text" name="nomor_rangka" class="form-control @error('nomor_rangka') is-invalid @enderror" value="{{ old('nomor_rangka') }}">
                    @error('nomor_rangka') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Nomor Polisi</label>
                    <input type="text" name="nomor_polisi" class="form-control text-uppercase @error('nomor_polisi') is-invalid @enderror" value="{{ old('nomor_polisi') }}" placeholder="B 1234 ABC">
                    @error('nomor_polisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Nomor BPKB</label>
                    <input type="text" name="nomor_bpkb" class="form-control @error('nomor_bpkb') is-invalid @enderror" value="{{ old('nomor_bpkb') }}">
                    @error('nomor_bpkb') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tanggal STNK (5 Tahunan)</label>
                    <input type="date" name="tanggal_stnk" class="form-control @error('tanggal_stnk') is-invalid @enderror" value="{{ old('tanggal_stnk') }}">
                    @error('tanggal_stnk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tanggal STNK (Tahunan)</label>
                    <input type="date" name="tanggal_pajak" class="form-control @error('tanggal_pajak') is-invalid @enderror" value="{{ old('tanggal_pajak') }}">
                    @error('tanggal_pajak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- 3. PEROLEHAN, LOKASI & FINANSIAL --}}
            <h6 class="font-weight-bold text-secondary mt-3 mb-3 border-bottom pb-2">3. Perolehan & Lokasi Fisik</h6>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Lokasi Fisik Barang <span class="text-danger">*</span></label>
                    <input type="text" name="Lok" class="form-control @error('Lok') is-invalid @enderror" value="{{ old('Lok') }}" placeholder="Ruang Rapat / Gudang Lt.2" required>
                    @error('Lok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Cara Perolehan <span class="text-danger">*</span></label>
                    <select name="cara_perolehan" class="form-control @error('cara_perolehan') is-invalid @enderror" required>
                        <option value="Pembelian" {{ old('cara_perolehan') == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                        <option value="Hibah" {{ old('cara_perolehan') == 'Hibah' ? 'selected' : '' }}>Hibah</option>
                        <option value="Sumbangan" {{ old('cara_perolehan') == 'Sumbangan' ? 'selected' : '' }}>Sumbangan</option>
                        <option value="Lainnya" {{ old('cara_perolehan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('cara_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tanggal Perolehan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_perolehan" class="form-control @error('tanggal_perolehan') is-invalid @enderror" value="{{ old('tanggal_perolehan', date('Y-m-d')) }}" required>
                    @error('tanggal_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Jumlah (Kuantitas) <span class="text-danger">*</span></label>
                    <input type="number" id="jumlah" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', 1) }}" min="1" required>
                    @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control @error('satuan') is-invalid @enderror" value="{{ old('satuan', 'Unit') }}" required>
                    @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Harga Satuan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                        <input type="text" id="harga_satuan" name="harga_satuan" class="form-control @error('harga_satuan') is-invalid @enderror" value="{{ old('harga_satuan') }}" placeholder="0" required>
                    </div>
                    @error('harga_satuan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Nilai Perolehan Total <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                        <input type="text" id="nilai_perolehan" name="nilai_perolehan" class="form-control bg-light font-weight-bold @error('nilai_perolehan') is-invalid @enderror" value="{{ old('nilai_perolehan') }}" readonly required>
                    </div>
                    @error('nilai_perolehan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- 4. STATUS & KONDISI --}}
            <h6 class="font-weight-bold text-secondary mt-3 mb-3 border-bottom pb-2">4. Status, Kondisi & File</h6>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Status Penggunaan <span class="text-danger">*</span></label>
                    <select name="status_penggunaan" class="form-control @error('status_penggunaan') is-invalid @enderror" required>
                        <option value="Digunakan Sendiri" {{ old('status_penggunaan') == 'Digunakan Sendiri' ? 'selected' : '' }}>Digunakan Sendiri</option>
                        <option value="Aktif" {{ old('status_penggunaan') == 'Aktif' ? 'selected' : '' }}>Aktif (Operasional)</option>
                        <option value="Tersedia" {{ old('status_penggunaan') == 'Tersedia' ? 'selected' : '' }}>Tersedia (Gudang/Idle)</option>
                    </select>
                    @error('status_penggunaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Kondisi Fisik <span class="text-danger">*</span></label>
                    <select name="kondisi" class="form-control @error('kondisi') is-invalid @enderror" required>
                        <option value="Baik" {{ old('kondisi', 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('kondisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label class="font-weight-bold">Foto Barang (Opsional)</label>
                <input type="file" name="foto" class="form-control-file @error('foto') is-invalid @enderror" accept="image/*">
                <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal: 2MB.</small>
                @error('foto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Keterangan / Catatan</label>
                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan') }}</textarea>
                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputJumlah      = document.getElementById('jumlah');
        const inputHargaSatuan = document.getElementById('harga_satuan');

        // Event Listener Format Rupiah
        inputHargaSatuan.addEventListener('input', function() {
            this.value = formatRupiah(this.value);
            hitungNilaiPerolehan();
        });

        inputJumlah.addEventListener('input', function() {
            hitungNilaiPerolehan();
        });

        if (inputHargaSatuan.value) {
            inputHargaSatuan.value = formatRupiah(inputHargaSatuan.value);
        }
        hitungNilaiPerolehan();
    });

    function formatRupiah(angka) {
        if (!angka) return '';
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
            split         = number_string.split(','),
            sisa          = split[0].length % 3,
            rupiah        = split[0].substr(0, sisa),
            ribuan        = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    }

    function hitungNilaiPerolehan() {
        let jumlah = parseInt(document.getElementById('jumlah').value) || 0;
        let hargaSatuanStr = document.getElementById('harga_satuan').value.replace(/\./g, '').split(',')[0];
        let hargaSatuan = parseInt(hargaSatuanStr) || 0;
        
        let total = jumlah * hargaSatuan;
        document.getElementById('nilai_perolehan').value = formatRupiah(total.toString());
    }
</script>
@endsection