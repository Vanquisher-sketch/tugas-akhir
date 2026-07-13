@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Data Peralatan & Mesin (KIB B) - {{ ucfirst($lokasi) }}
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.peralatan.store', ['lokasi' => $lokasi]) }}" method="POST">
            @csrf

            {{-- 1. IDENTITAS UTAMA BARANG --}}
            <h6 class="font-weight-bold text-secondary mb-3 border-bottom pb-2">1. Identitas Utama Barang</h6>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                    <input type="text" name="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" value="{{ old('kode_barang') }}" placeholder="Contoh: 02.06.01.01" required>
                    @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Lokasi Fisik <span class="text-danger">*</span></label>
                    <input type="text" name="Lok" class="form-control" value="{{ old('Lok') }}" placeholder="Gedung / Lantai" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">NIBAR</label>
                    <input type="text" name="nibr" class="form-control" value="{{ old('nibr') }}">
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Nomor Register</label>
                    <input type="text" name="nomor_register" class="form-control" value="{{ old('nomor_register') }}">
                </div>
            </div>

            {{-- 2. SPESIFIKASI & LEGALITAS KENDARAAN --}}
            <h6 class="font-weight-bold text-secondary mt-4 mb-3 border-bottom pb-2">2. Spesifikasi & Kendaraan (Opsional)</h6>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Merk / Tipe</label>
                    <input type="text" name="merk_tipe" class="form-control" value="{{ old('merk_tipe') }}">
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Nomor Rangka</label>
                    <input type="text" name="nomor_rangka" class="form-control" value="{{ old('nomor_rangka') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Nomor Polisi</label>
                    <input type="text" name="nomor_polisi" class="form-control" value="{{ old('nomor_polisi') }}" placeholder="Contoh: Z 1234 XY">
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Nomor BPKB</label>
                    <input type="text" name="nomor_bpkb" class="form-control" value="{{ old('nomor_bpkb') }}">
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tanggal STNK (5 Tahunan)</label>
                    <input type="date" name="tanggal_stnk" class="form-control" value="{{ old('tanggal_stnk') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Spesifikasi Barang</label>
                    <textarea name="spesifikasi_barang" class="form-control" rows="2">{{ old('spesifikasi_barang') }}</textarea>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Spesifikasi Lainnya</label>
                    <textarea name="spesifikasi_lainnya" class="form-control" rows="2">{{ old('spesifikasi_lainnya') }}</textarea>
                </div>
            </div>

            {{-- 3. PEROLEHAN & FINANSIAL --}}
            <h6 class="font-weight-bold text-secondary mt-4 mb-3 border-bottom pb-2">3. Perolehan & Finansial</h6>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Cara Perolehan <span class="text-danger">*</span></label>
                    <select name="cara_perolehan" class="form-control" required>
                        <option value="Pembelian" {{ old('cara_perolehan') == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                        <option value="Hibah" {{ old('cara_perolehan') == 'Hibah' ? 'selected' : '' }}>Hibah</option>
                        <option value="Sumbangan" {{ old('cara_perolehan') == 'Sumbangan' ? 'selected' : '' }}>Sumbangan</option>
                        <option value="Lainnya" {{ old('cara_perolehan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tanggal Perolehan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_perolehan" class="form-control" value="{{ old('tanggal_perolehan') }}" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Tenggat Pajak Tahunan (Kendaraan)</label>
                    <input type="date" name="tanggal_pajak" class="form-control" value="{{ old('tanggal_pajak') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" id="jumlah" name="jumlah" class="form-control" value="{{ old('jumlah', 1) }}" min="1" required oninput="hitungNilaiPerolehan()">
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', 'Unit') }}" required>
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                    <input type="text" id="harga_satuan" name="harga_satuan" class="form-control format-rupiah" value="{{ old('harga_satuan') }}" required oninput="hitungNilaiPerolehan()">
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">Nilai Perolehan Total (Rp) <span class="text-danger">*</span></label>
                    <input type="text" id="nilai_perolehan" name="nilai_perolehan" class="form-control format-rupiah bg-light" value="{{ old('nilai_perolehan') }}" readonly required>
                </div>
            </div>

            {{-- 4. STATUS & KONDISI --}}
            <h6 class="font-weight-bold text-secondary mt-4 mb-3 border-bottom pb-2">4. Status & Kondisi</h6>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Status Penggunaan <span class="text-danger">*</span></label>
                    <select name="status_penggunaan" class="form-control" required>
                        <option value="Aktif" {{ old('status_penggunaan') == 'Aktif' ? 'selected' : '' }}>Aktif (Digunakan)</option>
                        <option value="Tersedia" {{ old('status_penggunaan') == 'Tersedia' ? 'selected' : '' }}>Tersedia (Gudang/Idle)</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Kondisi Fisik <span class="text-danger">*</span></label>
                    <select name="kondisi" class="form-control" required>
                        <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="font-weight-bold">Keterangan / Catatan Lainnya</label>
                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Data Peralatan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Format Rupiah Otomatis
    const formatRupiahElements = document.querySelectorAll('.format-rupiah');
    
    formatRupiahElements.forEach(function(element) {
        element.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });
    });

    function formatRupiah(angka, prefix) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split         = number_string.split(','),
            sisa          = split[0].length % 3,
            rupiah        = split[0].substr(0, sisa),
            ribuan        = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    // Kalkulasi Total Nilai Perolehan
    function hitungNilaiPerolehan() {
        let jumlah = document.getElementById('jumlah').value;
        let hargaSatuanStr = document.getElementById('harga_satuan').value.replace(/\./g, '').replace(/,/g, '.');
        
        let hargaSatuan = parseFloat(hargaSatuanStr) || 0;
        let total = parseInt(jumlah) * hargaSatuan;

        // Tampilkan format ke input readonly
        let totalFormatted = total.toString().replace(/\./g, ',');
        document.getElementById('nilai_perolehan').value = formatRupiah(totalFormatted);
    }
</script>
@endsection