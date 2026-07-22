@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-edit mr-2"></i> Edit Data Peralatan & Mesin (KIB B) - {{ ucfirst($lokasi) }}
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.peralatan.update', ['lokasi' => $lokasi, 'kode_barang' => $peralatan->alat_kode_barang]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- 1. IDENTITAS UTAMA BARANG --}}
            <h6 class="font-weight-bold text-secondary mb-3 border-bottom pb-2">1. Identitas Utama Barang</h6>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control bg-light" value="{{ old('kode_barang', $peralatan->alat_kode_barang) }}" readonly required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang', $peralatan->alat_nama_barang) }}" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Lokasi Fisik <span class="text-danger">*</span></label>
                    <input type="text" name="Lok" class="form-control" value="{{ old('Lok', $peralatan->alat_lokasi_fisik) }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">NIBAR</label>
                    <input type="text" name="nibr" class="form-control" value="{{ old('nibr', $peralatan->alat_nibar) }}">
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Nomor Register</label>
                    <input type="text" name="nomor_register" class="form-control" value="{{ old('nomor_register', $peralatan->alat_nomor_register) }}">
                </div>
            </div>

            {{-- 2. SPESIFIKASI & LEGALITAS KENDARAAN --}}
            <h6 class="font-weight-bold text-secondary mt-4 mb-3 border-bottom pb-2">2. Spesifikasi & Kendaraan (Opsional)</h6>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Merk / Tipe</label>
                    <input type="text" name="merk_tipe" class="form-control" value="{{ old('merk_tipe', $peralatan->alat_merk_tipe) }}">
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Nomor Rangka</label>
                    <input type="text" name="nomor_rangka" class="form-control" value="{{ old('nomor_rangka', $peralatan->alat_nomor_rangka) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Nomor Polisi</label>
                    <input type="text" name="nomor_polisi" class="form-control text-uppercase" value="{{ old('nomor_polisi', $peralatan->alat_nomor_polisi) }}">
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Nomor BPKB</label>
                    <input type="text" name="nomor_bpkb" class="form-control" value="{{ old('nomor_bpkb', $peralatan->alat_nomor_bpkb) }}">
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tanggal STNK (5 Tahunan)</label>
                    <input type="date" name="tanggal_stnk" class="form-control" value="{{ old('tanggal_stnk', $peralatan->alat_tanggal_stnk) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Spesifikasi Barang</label>
                    <textarea name="spesifikasi_barang" class="form-control" rows="2">{{ old('spesifikasi_barang', $peralatan->alat_spesifikasi_barang) }}</textarea>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Spesifikasi Lainnya</label>
                    <textarea name="spesifikasi_lainnya" class="form-control" rows="2">{{ old('spesifikasi_lainnya', $peralatan->alat_spesifikasi_lainnya) }}</textarea>
                </div>
            </div>

            {{-- 3. PEROLEHAN & FINANSIAL --}}
            <h6 class="font-weight-bold text-secondary mt-4 mb-3 border-bottom pb-2">3. Perolehan & Finansial</h6>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Cara Perolehan <span class="text-danger">*</span></label>
                    <select name="cara_perolehan" class="form-control" required>
                        <option value="Pembelian" {{ old('cara_perolehan', $peralatan->alat_cara_perolehan) == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                        <option value="Hibah" {{ old('cara_perolehan', $peralatan->alat_cara_perolehan) == 'Hibah' ? 'selected' : '' }}>Hibah</option>
                        <option value="Sumbangan" {{ old('cara_perolehan', $peralatan->alat_cara_perolehan) == 'Sumbangan' ? 'selected' : '' }}>Sumbangan</option>
                        <option value="Lainnya" {{ old('cara_perolehan', $peralatan->alat_cara_perolehan) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tanggal Perolehan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_perolehan" class="form-control" value="{{ old('tanggal_perolehan', $peralatan->alat_tanggal_perolehan) }}" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tenggat Pajak Tahunan</label>
                    <input type="date" name="tanggal_pajak" class="form-control" value="{{ old('tanggal_pajak', $peralatan->alat_tanggal_pajak) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" id="jumlah" name="jumlah" class="form-control" value="{{ old('jumlah', $peralatan->alat_jumlah) }}" min="1" required oninput="hitungNilaiPerolehan()">
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $peralatan->alat_satuan) }}" required>
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Harga Satuan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                        <input type="text" id="harga_satuan" name="harga_satuan" class="form-control format-rupiah" value="{{ old('harga_satuan', number_format($peralatan->alat_harga_satuan, 0, ',', '.')) }}" required oninput="hitungNilaiPerolehan()">
                    </div>
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Nilai Perolehan Total <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                        <input type="text" id="nilai_perolehan" name="nilai_perolehan" class="form-control bg-light font-weight-bold" value="{{ old('nilai_perolehan', number_format($peralatan->alat_nilai_perolehan, 0, ',', '.')) }}" readonly required>
                    </div>
                </div>
            </div>

            {{-- 4. STATUS, KONDISI & FOTO --}}
            <h6 class="font-weight-bold text-secondary mt-4 mb-3 border-bottom pb-2">4. Status, Kondisi & Foto</h6>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Status Penggunaan <span class="text-danger">*</span></label>
                    <select name="status_penggunaan" class="form-control" required>
                        <option value="Aktif" {{ old('status_penggunaan', $peralatan->alat_status_penggunaan) == 'Aktif' ? 'selected' : '' }}>Aktif (Digunakan)</option>
                        <option value="Tersedia" {{ old('status_penggunaan', $peralatan->alat_status_penggunaan) == 'Tersedia' ? 'selected' : '' }}>Tersedia (Gudang/Idle)</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Kondisi Fisik <span class="text-danger">*</span></label>
                    <select name="kondisi" class="form-control" required>
                        <option value="Baik" {{ old('kondisi', $peralatan->alat_kondisi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ old('kondisi', $peralatan->alat_kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ old('kondisi', $peralatan->alat_kondisi) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Foto Barang</label>
                
                @if(isset($peralatan->alat_foto) && $peralatan->alat_foto != '')
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $peralatan->alat_foto) }}" alt="Foto Barang" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                @endif
                
                <input type="file" name="foto" class="form-control-file @error('foto') is-invalid @enderror" accept="image/*">
                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, JPEG, PNG. Ukuran maksimal: 2MB.</small>
                @error('foto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Keterangan / Catatan Lainnya</label>
                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $peralatan->alat_keterangan) }}</textarea>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    const formatRupiahElements = document.querySelectorAll('.format-rupiah');
    formatRupiahElements.forEach(function(element) {
        element.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });
    });

    function formatRupiah(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split         = number_string.split(','),
            sisa          = split[0].length % 3,
            rupiah        = split[0].substr(0, sisa),
            ribuan        = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
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